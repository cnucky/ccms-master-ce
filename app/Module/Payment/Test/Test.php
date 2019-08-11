<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-5
 * Time: 下午8:46
 */

namespace App\Module\Payment\Test;


use App\Module\Contract\PaymentModule;
use App\Module\Exception\ModuleException;
use App\Module\PayNotifyResult;
use App\Module\RefundNotifyResult;
use App\Module\Resource\Payment\FormField;
use App\Module\Resource\Payment\PaymentModuleConstructor;
use App\Module\Resource\Payment\PayResponse;
use App\PaymentTrade;
use App\PaymentTradeRefund;
use Illuminate\Http\Request;

class Test implements PaymentModule
{
    use PaymentModuleConstructor;

    /**
     * @inheritDoc
     */
    public function pay(Request $request, PaymentTrade $paymentTrade, $notifyURL, $returnURL)
    {
        // TODO: Implement pay() method.
    }

    /**
     * @inheritDoc
     */
    public function refund(Request $request, PaymentTradeRefund $paymentTradeRefund, $notifyURL)
    {
        // TODO: Implement refund() method.
    }

    /**
     * @inheritDoc
     */
    public function payNotify(Request $request): PayNotifyResult
    {
        // TODO: Implement notify() method.
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
        return "测试模块";
    }

    /**
     * @inheritDoc
     */
    public static function getVersion(): string
    {
        return "v0.0.1";
    }

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return "付款模块框架测试专用模块";
    }

    /**
     * @inheritDoc
     */
    public static function getAvailableSettings(): array
    {
        return [
            "id" => FormField::create("ID"),
            "key" => FormField::create("秘钥")->withType("password"),
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getAvailableChannels()
    {
        return null;
    }
}