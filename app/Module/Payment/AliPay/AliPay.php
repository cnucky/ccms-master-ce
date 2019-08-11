<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-5
 * Time: 下午2:54
 */

namespace App\Module\Payment\AliPay;


use App\Module\Constants\Payment\ValidationResult;
use App\Module\Contract\PaymentModule;
use App\Module\Exception\CancelRefundException;
use App\Module\Exception\ModuleException;
use App\Module\Exception\PaymentModuleException;
use App\Module\PayNotifyResult;
use App\Module\RefundNotifyResult;
use App\Module\Resource\Payment\FormField;
use App\Module\Resource\Payment\NotifyResult;
use App\Module\Resource\Payment\PaymentModuleConstructor;
use App\Module\Resource\Payment\PayResponse;
use App\PaymentTrade;
use App\PaymentTradeRefund;
use Illuminate\Http\Request;

class AliPay implements PaymentModule, PaymentModule\Notifiable
{
    use PaymentModuleConstructor;

    /**
     * @inheritDoc
     */
    public function pay(Request $request, PaymentTrade $paymentTrade, $notifyURL, $returnURL)
    {
        if ($this->currencyCode !== "CNY")
            throw new PaymentModuleException("Only CNY is supported");

            self::loadAliPaySDK();

        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setSubject(sprintf("用户: #%d %s - 余额充值", $paymentTrade->user->id, $paymentTrade->user->email));
        $payRequestBuilder->setTotalAmount($paymentTrade->fee);
        $payRequestBuilder->setOutTradeNo($paymentTrade->no);


        $aop = new \AlipayTradeService($this->getConfiguration());

        $response = $aop->pagePay($payRequestBuilder, $returnURL, $notifyURL);

        return $response;
    }

    /**
     * @inheritDoc
     */
    public function refund(Request $request, PaymentTradeRefund $paymentTradeRefund, $notifyURL)
    {
        self::loadAliPaySDK();
        require_once __DIR__ . "/SDK/pagepay/buildermodel/AlipayTradeRefundContentBuilder.php";

        //构造参数
        $RequestBuilder = new \AlipayTradeRefundContentBuilder();
        $RequestBuilder->setOutTradeNo($paymentTradeRefund->trade->no);
        $RequestBuilder->setRefundAmount($paymentTradeRefund->fee);
        $RequestBuilder->setOutRequestNo($paymentTradeRefund->no);
        $RequestBuilder->setRefundReason("自助退款");

        $aop = new \AlipayTradeService($this->getConfiguration());

        $response = $aop->Refund($RequestBuilder);

        $this->logger->log($response, LogType::TYPE_REFUND_API_RESPONSE);

        if ($response->code === "10000")
            return $response->trade_no;
        throw new CancelRefundException($response->msg);
    }

    /**
     * @inheritDoc
     */
    public function notify(Request $request): NotifyResult
    {
        self::loadAliPaySDK();

        $arr = $request->all();
        $alipaySevice = new \AlipayTradeService($this->getConfiguration());

        if (array_key_exists("gmt_refund", $arr) && array_key_exists("out_biz_no", $arr)) {
            $payNotifyResult = new RefundNotifyResult();
            $payNotifyResult->withId($arr["out_biz_no"]);
        } else {
            $payNotifyResult = new PayNotifyResult();
            $payNotifyResult->withId($arr["out_trade_no"]);
        }

        $result = $alipaySevice->check($arr);

        if ($result) {
            $payNotifyResult
                ->withFee($request->total_amount)
                ->withTransactionId($request->trade_no)
                ->withView("success")
            ;

            if ($request->trade_status === "TRADE_SUCCESS" || $request->trade_status === "TRADE_FINISHED") {
                $payNotifyResult->withResult(ValidationResult::CORRECT_SIGNATURE);
            } else {
                $payNotifyResult->withResult(ValidationResult::CORRECT_SIGNATURE_AND_RETURN);
            }
        }

        return $payNotifyResult;
    }

    public function refundNotify(Request $request): RefundNotifyResult
    {
        return $this->notify($request);
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return "支付宝";
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
        return "支付宝新即时到账接口模块";
    }

    /**
     * @inheritDoc
     */
    public static function getAvailableSettings(): array
    {
        return [
            "appId" => FormField::create("应用ID"),
            "signType" => FormField::create("签名类型")->withType("dropdown")->withOptions(["RSA2" => "RSA2 (Recommended)", "RSA" => "RSA"]),
            "alipayPublicKey" => FormField::create("支付宝公钥")->withType("textarea"),
            "merchantPrivateKey" => FormField::create("商户私钥")->withType("textarea"),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getAvailableChannels()
    {
        return null;
    }

    private function getAppId()
    {
        return $this->moduleSettings["appId"];
    }

    private function getSignType()
    {
        return $this->moduleSettings["signType"];
    }

    private function getAliPayPublicKey()
    {
        return $this->moduleSettings["alipayPublicKey"];
    }

    private function getMerchantPrivateKey()
    {
        return $this->moduleSettings["merchantPrivateKey"];
    }

    private function getConfiguration()
    {
        return [
            //应用ID,您的APPID。
            'app_id' => $this->getAppId(),
            //商户私钥
            'merchant_private_key' => $this->getMerchantPrivateKey(),
            //编码格式
            'charset' => "UTF-8",
            //签名方式
            'sign_type' => $this->getSignType(),
            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => $this->getAliPayPublicKey(),
        ];
    }

    private static function loadAliPaySDK()
    {
        require_once __DIR__ . "/SDK/pagepay/service/AlipayTradeService.php";
        require_once __DIR__ . "/SDK/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php";
    }
}