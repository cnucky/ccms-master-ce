<?php

namespace App\Http\Controllers\PaymentModule;

use App\Module\Constants\Payment\ModuleStatus;
use App\Module\Constants\Payment\PayResponseType;
use App\Module\Contract\PaymentModule\PayNotifiable;
use App\Module\Resource\Payment\PayResponse;
use App\PaymentModule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PayController extends Controller
{

    public function available(Request $request)
    {
        return [
            "result" => true,
            "available" => PaymentModule::query()->where("status", ModuleStatus::STATUS_ENABLED)->orderBy("order")->get(["id", "name"]),
        ];
    }

    public function pay(Request $request, PaymentModule $paymentModule)
    {
        $this->validate($request, [
            "fee" => [
                "required",
                'regex:/^[0-9]{1,9}(\.[0-9]{0,2}){0,1}$/',
                "min:0.1",
            ]
        ]);

        if ($paymentModule->status !== ModuleStatus::STATUS_ENABLED)
            return response("", 403);
        $trade = $paymentModule->newTrade($request->user()->id, $request->channel, $request->fee);

        $basicPaymentModule = $paymentModule->getBasicPaymentModule();
        if ($basicPaymentModule instanceof PayNotifiable) {
            $notifyURL = $this->buildPayNotifyURL($paymentModule->id, $trade->no);
        } else {
            $notifyURL = $this->buildNotifyURL($paymentModule->id, $trade->no);
        }

        $modulePayResponse = $basicPaymentModule->pay(
            $request,
            $trade,
            $notifyURL,
            $this->buildReturnURL($paymentModule->id, $trade->no)
        );

        if (is_string($modulePayResponse)) {
            $modulePayResponse = new PayResponse(PayResponseType::TYPE_URL, $modulePayResponse);
        } else if (is_array($modulePayResponse)) {
            $modulePayResponse = new PayResponse(PayResponseType::TYPE_FORM_FIELDS, $modulePayResponse);
        }

        return ["result" => true, "tradeId" => $trade->id, "modulePayResponse" => $modulePayResponse];
    }

    private function buildNotifyURL($paymentModuleId, $tradeNo)
    {
        $notifyURLPrefix = env("PAYMENT_MODULE_NOTIFY_SCHEMA_AND_HTTP_HOST", null);
        if (is_null($notifyURLPrefix))
            return route("paymentModules.notify", [$paymentModuleId, $tradeNo]);
        return $notifyURLPrefix . route("paymentModules.notify", [$paymentModuleId, $tradeNo], false);
    }

    private function buildPayNotifyURL($paymentModuleId, $tradeNo)
    {
        $notifyURLPrefix = env("PAYMENT_MODULE_NOTIFY_SCHEMA_AND_HTTP_HOST", null);
        if (is_null($notifyURLPrefix))
            return route("paymentModules.payNotify", [$paymentModuleId, $tradeNo]);
        return $notifyURLPrefix . route("paymentModules.payNotify", [$paymentModuleId, $tradeNo], false);
    }

    private function buildReturnURL($paymentModuleId, $tradeNo)
    {
        return route("paymentModules.payReturn", [$paymentModuleId, $tradeNo]);
    }
}
