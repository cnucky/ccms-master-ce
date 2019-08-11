<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-7
 * Time: 上午2:08
 */

namespace App\Module\Payment\WxPay;


use App\Module\Constants\Payment\PayResponseType;
use App\Module\Constants\Payment\ValidationResult;
use App\Module\Contract\PaymentModule;
use App\Module\Exception\CancelRefundException;
use App\Module\Exception\ModuleException;
use App\Module\Exception\PaymentModuleException;
use App\Module\PayNotifyResult;
use App\Module\RefundNotifyResult;
use App\Module\Resource\FeeProcessor;
use App\Module\Resource\Payment\FormField;
use App\Module\Resource\Payment\PaymentModuleConstructor;
use App\Module\Resource\Payment\PayResponse;
use App\PaymentTrade;
use App\PaymentTradeRefund;
use Illuminate\Http\Request;

class WxPay implements PaymentModule, PaymentModule\DedicatedNotifiable
{
    use PaymentModuleConstructor;

    /**
     * @inheritDoc
     */
    public function pay(Request $request, PaymentTrade $paymentTrade, $notifyURL, $returnURL)
    {
        self::load();

        $config = $this->makeConfig();

        $notify = new \NativePay();

        $timeZone = new \DateTimeZone("Asia/Shanghai");
        $now = new \DateTime();
        $now->setTimezone($timeZone);

        $text = sprintf("用户: #%d %s - 余额充值", $paymentTrade->user->id, $paymentTrade->user->email);

        $input = new \WxPayUnifiedOrder();
        $input->SetBody($text);
        $input->SetOut_trade_no($paymentTrade->no);
        $input->SetTotal_fee(FeeProcessor::multiply100($paymentTrade->fee));
        $input->SetTime_start($now->format("YmdHis"));

        $now->add(new \DateInterval("PT600S"));
        $input->SetTime_expire($now->format("YmdHis"));
        $input->SetGoods_tag("自助充值");
        $input->SetNotify_url($notifyURL);
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($paymentTrade->id);
        $input->SetFee_type($this->currencyCode);


        $result = $notify->GetPayUrl($input, $config);
        if ($result["return_code"] !== "SUCCESS")
            throw new PaymentModuleException($result["return_msg"]);
        return new PayResponse(PayResponseType::TYPE_QR_CODE, $result["code_url"]);
    }

    /**
     * @inheritDoc
     */
    public function refund(Request $request, PaymentTradeRefund $paymentTradeRefund, $notifyURL)
    {
        self::load();
        $config = $this->makeConfig();
        $config->GetSSLCertPath($certificatePath, $privateKeypPath);
        if (!file_exists($certificatePath) || !file_exists($privateKeypPath))
            throw new CancelRefundException("API certificate not found");

        $totalFee = FeeProcessor::multiply100($paymentTradeRefund->trade->fee);
        $refundFee = FeeProcessor::multiply100($paymentTradeRefund->fee);

        $input = new \WxPayRefund();
        $input->SetOut_trade_no($paymentTradeRefund->trade->no);
        $input->SetTotal_fee($totalFee);
        $input->SetRefund_fee($refundFee);
        // $input->set("notify_url", $notifyURL);

        $config = $this->makeConfig();
        $input->SetOut_refund_no($paymentTradeRefund->no);
        $input->SetOp_user_id($config->GetMerchantId());

        $response = \WxPayApi::refund($config, $input);
        $this->logger->log($response);
        if ($response["return_code"] === "SUCCESS")
            return $response["refund_id"];
        throw new CancelRefundException();
    }

    /**
     * @inheritDoc
     */
    public function payNotify(Request $request): PayNotifyResult
    {
        self::load();
        require_once __DIR__ . "/SDK/example/notify.php";

        $payNotifyResult = new PayNotifyResult();
        $payNotifyResult->withView("");

        $notify = new \PayNotifyCallBack(function ($objData, $config) use ($payNotifyResult) {
            $values = $objData->getValues();
            $payNotifyResult
                ->withResult(ValidationResult::CORRECT_SIGNATURE)
                ->withId($values["out_trade_no"])
                ->withTransactionId($values["transaction_id"])
                ->withFee(FeeProcessor::divide100($values["total_fee"]))
            ;
        }, $this->makeConfig());
        $notify->Handle($this->makeConfig(), false);
        return $payNotifyResult;
    }

    /**
     * @inheritDoc
     */
    public function refundNotify(Request $request): RefundNotifyResult
    {
        // TODO: Implement refundNotify() method.
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return "微信支付";
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
        return "微信支付";
    }

    /**
     * @inheritDoc
     */
    public static function getAvailableSettings(): array
    {
        return [
            "appId" => FormField::create("公众账号ID"),
            "mchId" => FormField::create("商户号"),
            "key" => FormField::create("API秘钥")->withType("password"),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getAvailableChannels()
    {
        // TODO: Implement getAvailableChannels() method.
    }

    private function makeConfig()
    {
        return new WxPayConfig($this->moduleSettings["appId"], $this->moduleSettings["mchId"], $this->moduleSettings["key"]);
    }

    private static function load()
    {
        require_once __DIR__ . "/SDK/lib/WxPay.Api.php";
        require_once __DIR__ . "/SDK/lib/WxPay.Config.Interface.php";
        require_once __DIR__ . "/SDK/example/WxPay.NativePay.php";
    }
}