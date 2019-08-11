<?php

namespace App\Http\Controllers\PaymentModule;

use App\Http\Controllers\SPA\ClientAreaSPAController;
use App\Module\Constants\Payment\ValidationResult;
use App\Module\Contract\PaymentModule\NotifyType;
use App\Module\Contract\PaymentModule\PayReturnable;
use App\Module\Exception\ModuleNotFoundException;
use App\Module\Resource\Payment\NotifyResult;
use App\PaymentModule;
use App\PaymentModuleNotifyLog;
use App\PaymentTrade;
use App\PaymentTradeRefund;
use App\Utils\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class NotifyController extends Controller
{
    public function notify(Request $request, PaymentModule $paymentModule)
    {
        $notifyLog = $this->logNotifyRequest($request);
        $notifyResult = $paymentModule->getBasicPaymentModule()->notify($request);
        return $this->notifyResultHandle($paymentModule, $notifyResult, $notifyLog);
    }

    public function payNotify(Request $request, PaymentModule $paymentModule)
    {
        $notifyLog = $this->logNotifyRequest($request);
        $notifyResult = $paymentModule->getBasicPaymentModule()->payNotify($request);
        return $this->notifyResultHandle($paymentModule, $notifyResult, $notifyLog);
    }

    public function payReturn(Request $request, PaymentModule $paymentModule)
    {
        try {
            $loader = $paymentModule->getPaymentModuleLoader();
            // Invoke payReturn() interface if it's a PayReturnable
            if ($loader->getReflectionClass()->implementsInterface(PayReturnable::class)) {
                $basicModule = $loader->newInstance($paymentModule);
                try {
                    $payReturnResult = $basicModule->payReturn($request);
                    $this->notifyResultHandle($paymentModule, $payReturnResult);
                } catch (\Exception $e) {
                    Log::error($e->getMessage(), ["exception" => $e]);
                }
            }
        } catch (ModuleNotFoundException $e) {
            Common::logException($e);
        }

        return ClientAreaSPAController::view();
    }

    public function refundNotify(Request $request, PaymentModule $paymentModule)
    {
        $notifyLog = PaymentModuleNotifyLog::query()->create([
            "remote_address" => $request->ip(),
            "request_url" => $request->fullUrl(),
            "request_headers" => json_encode($request->headers->all()),
            "raw_request_body" => $request->getContent(),
        ]);

        $notifyResult = $paymentModule->getBasicPaymentModule()->refundNotify($request);
        return $this->notifyResultHandle($paymentModule, $notifyResult, $notifyLog);
    }

    private function notifyResultHandle(PaymentModule $paymentModule, NotifyResult $notifyResult, PaymentModuleNotifyLog $notifyLog = null)
    {
        if (!is_null($notifyLog)) {
            $notifyLog->update([
                "type" => $notifyResult->type,
                "validate_result" => $notifyResult->result,
            ]);
        }

        if ($notifyResult->result === ValidationResult::CORRECT_SIGNATURE) {
            if (!is_null($notifyLog)) {
                $notifyLog->update([
                    "trade_no" => $notifyResult->no,
                ]);
            }

            switch ($notifyResult->type) {
                case NotifyType::TYPE_PAY_NOTIFY:
                case NotifyType::TYPE_PAY_RETURN:
                    $this->payNotifyResultCorrectSignatureHandle($paymentModule, $notifyResult);
                    break;
                case NotifyType::TYPE_REFUND_NOTIFY:
                    $this->refundNotifyResultCorrectSignatureHandle($paymentModule, $notifyResult);
                    break;
            }
        }

        return $notifyResult->view;
    }

    private function payNotifyResultCorrectSignatureHandle(PaymentModule $paymentModule, NotifyResult $notifyResult)
    {
        $tradeNo = $notifyResult->no;

        /**
         * @var PaymentTrade $paymentTrade
         */
        $paymentTrade = PaymentTrade::query()
            ->where("no", $tradeNo)
            ->where("payment_module_id", $paymentModule->id)
            ->where("fee", $notifyResult->fee)
            ->firstOrFail()
        ;

        $paymentTrade->paid($notifyResult->transactionId);

        $paymentTrade->user->autoChangeUserQuota();
    }

    private function refundNotifyResultCorrectSignatureHandle(PaymentModule $paymentModule, NotifyResult $notifyResult)
    {
        $tradeNo = $notifyResult->no;

        /**
         * @var PaymentTradeRefund $paymentTradeRefund
         */
        $paymentTradeRefund = PaymentTradeRefund::query()
            ->where("no", $tradeNo)
            ->firstOrFail()
        ;

        /**
         * @var PaymentTrade $paymentTrade
         */
        $paymentTrade = $paymentTradeRefund
            ->trade()
            ->where("payment_module_id", $paymentModule->id)
            ->firstOrFail()
        ;

        $paymentTrade->refundPreparationCommit($paymentTradeRefund, $notifyResult->transactionId);
    }

    private function logNotifyRequest(Request $request)
    {
        return PaymentModuleNotifyLog::query()->create([
            "remote_address" => $request->ip(),
            "request_url" => $request->fullUrl(),
            "request_headers" => json_encode($request->headers->all()),
            "raw_request_body" => $request->getContent(),
        ]);
    }
}
