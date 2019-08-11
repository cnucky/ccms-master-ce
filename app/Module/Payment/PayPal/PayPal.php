<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-7
 * Time: 下午4:07
 */

namespace App\Module\Payment\PayPal;


use App\Module\Constants\Payment\TradeStatus;
use App\Module\Constants\Payment\ValidationResult;
use App\Module\Contract\PaymentModule;
use App\Module\Exception\CancelRefundException;
use App\Module\Exception\PaymentModuleException;
use App\Module\PayNotifyResult;
use App\Module\RefundNotifyResult;
use App\Module\Resource\Payment\FormField;
use App\Module\Resource\Payment\PaymentModuleConstructor;
use App\Module\Resource\Payment\PayReturnResult;
use App\PaymentTrade;
use App\PaymentTradeRefund;
use BraintreeHttp\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use PayPal\Api\VerifyWebhookSignature;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Payments\CapturesRefundRequest;

class PayPal implements PaymentModule, PaymentModule\DedicatedNotifiable, PaymentModule\PayReturnable
{
    use PaymentModuleConstructor;

    /**
     * @inheritDoc
     */
    public function pay(Request $request, PaymentTrade $paymentTrade, $notifyURL, $returnURL)
    {
        self::load();

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            'intent' => 'CAPTURE',
            'application_context' =>
                [
                    'return_url' => $returnURL,
                    'cancel_url' => route("billing.addCredit"),
                ],
            'purchase_units' =>
                [
                    [
                        'reference_id' => $paymentTrade->no,
                        'invoice_id' => $paymentTrade->no,
                        'description' => sprintf("用户: #%d %s - 余额充值", $paymentTrade->user->id, $paymentTrade->user->email),
                        'amount' =>
                            [
                                'currency_code' => $this->currencyCode,
                                'value' => $paymentTrade->fee,
                            ]
                    ]
                ]
        ];
        // 3. Call PayPal to set up a transaction
        $client = $this->makeClient();
        $response = $client->execute($request);

        $this->logger->log($response, $paymentTrade->id, Constants::TYPE_ORDER_CREATE_RESPONSE);

        // 4. Return a successful response to the client.
        $result = $response->result;
        if ($result->status !== "CREATED")
            throw new PaymentModuleException();

        $paymentTrade->update([
            "payment_module_data" => $result->id,
        ]);

        return $this->findApproveLink($result);
    }

    /**
     * @inheritDoc
     */
    public function refund(Request $request, PaymentTradeRefund $paymentTradeRefund, $notifyURL)
    {
        $request = new CapturesRefundRequest($paymentTradeRefund->trade->transaction_id);
        $request->body = [
            'amount' =>
                [
                    'value' => $paymentTradeRefund->fee,
                    'currency_code' => $this->currencyCode,
                ],
            'invoice_id' => $paymentTradeRefund->no,
        ];
        $client = $this->makeClient();

        try {
            $response = $client->execute($request);
            $this->logger->log($response, $paymentTradeRefund->id, Constants::TYPE_REFUND_CAPTURE_RESPONSE);
            if ($response->statusCode !== 201) {
                throw new CancelRefundException();
            }
            if ($response->result->status === "COMPLETED") {
                return $response->result->id;
            }
            if ($response->result->status === "PENDING") {
                return null;
            }
            if ($response->result->status === "CANCELLED") {
                throw new CancelRefundException();
            }
        } catch (HttpException $ex) {
            $this->logger->log($ex->getMessage(), $paymentTradeRefund->id, Constants::TYPE_REFUND_HTTP_ERROR);
            $data = json_decode($ex->getMessage());
            if ($data) {
                if ($data->name === "UNPROCESSABLE_ENTITY" || $data->name === "RESOURCE_NOT_FOUND") {
                    throw new CancelRefundException();
                }
            }
            throw $ex;
        }
        return null;
    }

    public function payReturn(Request $request): PayReturnResult
    {
        $payReturnResult = new PayReturnResult();

        try {
            $paymentTrade = PaymentTrade::query()
                ->where("no", $request->tradeNo)
                ->where("status", TradeStatus::STATUS_UNPAID)
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $payReturnResult->withResult(ValidationResult::RETURN_DIRECTLY);
            goto RETURN_RESULT;
        }

        $orderId = $paymentTrade->payment_module_data;

        self::load();

        $client = $this->makeClient();

        $request = new OrdersCaptureRequest($orderId);
        $request->prefer('return=representation');
        try {
            // Call API with your client and get a response for your call
            $response = $client->execute($request);

            $this->logger->log($response, $paymentTrade->id, Constants::TYPE_ORDER_CAPTURE_RESPONSE);

            if ($response->statusCode === 201) {
                $result = $response->result;
                $payReturnResult
                    ->withResult(ValidationResult::CORRECT_SIGNATURE)
                    ->withId($result->purchase_units[0]->reference_id)
                    ->withFee($result->purchase_units[0]->amount->value)
                    ->withTransactionId($result->purchase_units[0]->payments->captures[0]->id)
                ;
            }
        } catch (HttpException $ex) {
            throw new PaymentModuleException($ex->getMessage(), $ex->statusCode, $ex);
        }

        RETURN_RESULT:
        return $payReturnResult;
    }

    /**
     * @inheritDoc
     */
    public function payNotify(Request $request): PayNotifyResult
    {
        $payNotifyResult = new PayNotifyResult();

        if ($this->webhookSignatureVerify($request, $this->moduleSettings["paymentCaptureCompletedWebHookId"])) {
            $values = $request->all();
            if ($values["event_type"] === "PAYMENT.CAPTURE.COMPLETED") {
                $payNotifyResult->withView("SUCCESS");
                $payNotifyResult
                    ->withId($values["resource"]["invoice_id"])
                    ->withResult(ValidationResult::CORRECT_SIGNATURE)
                    ->withFee($values["resource"]["amount"]["value"])
                ;
            } else {
                $payNotifyResult->withView("SUCCESS_RETURN");
                $payNotifyResult->withResult(ValidationResult::CORRECT_SIGNATURE_AND_RETURN);
            }
        } else {
            $payNotifyResult->withView(response("INCORRECT_SIGNATURE", 403));
        }

        return $payNotifyResult;
    }

    /**
     * @inheritDoc
     */
    public function refundNotify(Request $request): RefundNotifyResult
    {
        $refundNotifyResult = new RefundNotifyResult();

        if ($this->webhookSignatureVerify($request, $this->moduleSettings["paymentCaptureRefundedWebHookId"])) {
            $values = $request->all();
            if ($values["event_type"] === "PAYMENT.CAPTURE.REFUNDED") {
                $refundNotifyResult->withView("SUCCESS");
                $refundNotifyResult
                    ->withId($values["resource"]["invoice_id"])
                    ->withResult(ValidationResult::CORRECT_SIGNATURE)
                ;
            } else {
                $refundNotifyResult->withView("SUCCESS_RETURN");
                $refundNotifyResult->withResult(ValidationResult::CORRECT_SIGNATURE_AND_RETURN);
            }
        } else {
            $refundNotifyResult->withView(response("INCORRECT_SIGNATURE", 403));
        }

        return $refundNotifyResult;
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return "PayPal";
    }

    /**
     * @inheritDoc
     */
    public static function getVersion(): string
    {
        return "v0.1";
    }

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return "PayPal v2 REST API server-side module";
    }

    /**
     * @inheritDoc
     */
    public static function getAvailableSettings(): array
    {
        return [
            "sandboxMode" => FormField::create("SandboxEnvironment")->withType("dropdown")->withOptions([1 => "yes", 0 => "no"]),
            "CLIENT_ID" => FormField::create("Client ID"),
            "CLIENT_SECRET" => FormField::create("Client secret")->withType("password"),
            "paymentCaptureCompletedWebHookId" => FormField::create("Payment capture completed web hook id"),
            "paymentCaptureRefundedWebHookId" => FormField::create("Payment capture refunded web hook id"),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getAvailableChannels()
    {
        // TODO: Implement getAvailableChannels() method.
    }

    private function makeSandboxEnvironment()
    {
        return new SandboxEnvironment($this->moduleSettings["CLIENT_ID"], $this->moduleSettings["CLIENT_SECRET"]);
    }

    private function makeProductionEnvironment()
    {
        return new ProductionEnvironment($this->moduleSettings["CLIENT_ID"], $this->moduleSettings["CLIENT_SECRET"]);
    }

    private function makeClient()
    {
        if ($this->moduleSettings["sandboxMode"]) {
            $payPalEnvironment = $this->makeSandboxEnvironment();
        } else {
            $payPalEnvironment = $this->makeProductionEnvironment();
        }
        return new PayPalHttpClient($payPalEnvironment);
    }

    private function makeAPIContext()
    {
        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $this->moduleSettings["CLIENT_ID"],
                $this->moduleSettings["CLIENT_SECRET"]
            )
        );

        $mode = "sandbox";
        if (!$this->moduleSettings["sandboxMode"]) {
            $mode = "live";
        }

        $apiContext->setConfig(
            array(
                'mode' => $mode,
                // 'log.LogEnabled' => true,
                // 'log.FileName' => '../PayPal.log',
                // 'log.LogLevel' => 'DEBUG', // PLEASE USE `INFO` LEVEL FOR LOGGING IN LIVE ENVIRONMENTS
                // 'cache.enabled' => true,
                //'cache.FileName' => '/PaypalCache' // for determining paypal cache directory
                // 'http.CURLOPT_CONNECTTIMEOUT' => 30
                // 'http.headers.PayPal-Partner-Attribution-Id' => '123123123'
                //'log.AdapterFactory' => '\PayPal\Log\DefaultLogFactory' // Factory class implementing \PayPal\Log\PayPalLogFactory
            )
        );

        return $apiContext;
    }

    private function findApproveLink($result)
    {
        foreach ($result->links as $link) {
            if ($link->rel === "approve")
                return $link->href;
        }
        throw new PaymentModuleException("Approve link not found");
    }

    private function webhookSignatureVerify(Request $request, $webhookId)
    {
        $postBody = $request->getContent();
        $signatureVerification = new VerifyWebhookSignature();
        $signatureVerification->setRequestBody($postBody);

        $signatureVerification->setAuthAlgo($request->header('PAYPAL-AUTH-ALGO'));
        $signatureVerification->setTransmissionId($request->header('PAYPAL-TRANSMISSION-ID'));
        $signatureVerification->setCertUrl($request->header('PAYPAL-CERT-URL'));
        $signatureVerification->setWebhookId($webhookId); // Note that the Webhook ID must be a currently valid Webhook that you created with your client ID/secret.
        $signatureVerification->setTransmissionSig($request->header('PAYPAL-TRANSMISSION-SIG'));
        $signatureVerification->setTransmissionTime($request->header('PAYPAL-TRANSMISSION-TIME'));

        $response = $signatureVerification->post($this->makeAPIContext());

        $this->logger->log($response, $webhookId, Constants::TYPE_ORDER_CAPTURE_RESPONSE);

        return $response->getVerificationStatus() === "SUCCESS";
    }

    private static function load()
    {
        // require_once __DIR__ . "/vendor/autoload.php";
    }
}