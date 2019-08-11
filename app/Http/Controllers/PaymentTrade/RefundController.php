<?php

namespace App\Http\Controllers\PaymentTrade;

use App\Constants\CreditRecordType;
use App\Constants\GlobalErrorCode;
use App\Exceptions\NegativeCreditOperationException;
use App\Module\Constants\Payment\ModuleStatus;
use App\Module\Constants\Payment\TradeRefundStatus;
use App\Module\Contract\PaymentModule\RefundNotifiable;
use App\Module\Exception\CancelRefundException;
use App\PaymentModule;
use App\PaymentTrade;
use App\PaymentTradeRefund;
use App\User;
use App\UserCreditRecord;
use App\Utils\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class RefundController extends Controller
{
    public function index(Request $request, PaymentTrade $paymentTrade)
    {
        return [
            "result" => true,
            "paymentTrade" => $paymentTrade,
            "userCredit" => $paymentTrade->user->credit,
            "refundRecords" => $paymentTrade->refunds()->orderByDesc("id")->get(),
        ];
    }

    public function refund(Request $request, PaymentTrade $paymentTrade)
    {
        $this->validate($request, [
            "fee" => [
                "required",
                "numeric",
                "between:0.01," . $paymentTrade->refundable_fee,
                'regex:/^[0-9]{1,9}(\.[0-9]{0,2}){0,1}$/',
            ]
        ]);

        /**
         * @var User $user
         */
        $user = $request->user();
        if ($user->instances()->count() || $user->localVolumes()->count() || $user->ipv4s()->count() || $user->ipv6s()->count()) {
            return ["result" => false, "errno" => GlobalErrorCode::RELEASE_ALL_RESOURCE_IS_REQUIRED];
        }

        if ($this->validatePaymentModuleStatus($paymentTrade) === false) {
            return ["result" => false, "message" => "Module not available"];
        }

        $fee = $request->fee;

        try {
            $refundStatus = $this->refundViaPaymentModule($request, $fee, $paymentTrade);
            $indexResponse = $this->index($request, $paymentTrade->refresh());
            $indexResponse["refundStatus"] = $refundStatus;
        } catch (NegativeCreditOperationException $negativeCreditOperationException) {
            throw $negativeCreditOperationException;
        } catch (CancelRefundException $e) {
            $indexResponse = $this->index($request, $paymentTrade->refresh());
            $indexResponse["result"] = false;
        } catch (\Throwable $e) {
            $indexResponse = $this->index($request, $paymentTrade->refresh());
            $indexResponse["refundStatus"] = 0;
        }

        $indexResponse["newCredit"] = $user->refresh()->credit;
        return $indexResponse;
    }

    public function refundForAdmin(Request $request, PaymentTrade $paymentTrade)
    {
        $this->validate($request, [
            "fee" => [
                "required",
                "numeric",
                "between:0.01," . $paymentTrade->refundable_fee,
                'regex:/^[0-9]{1,9}(\.[0-9]{0,2}){0,1}$/',
            ],
            "transactionId" => "nullable|max:191",
        ]);

        $fee = $request->fee;

        $paymentTradeController = new PaymentTradeController();

        if (intval($request->type) === 0) {
            if ($this->validatePaymentModuleStatus($paymentTrade) === false) {
                return ["result" => false, "message" => "Module not available"];
            }
            try {
                $refundStatus = $this->refundViaPaymentModule($request, $fee, $paymentTrade, false);
                $showResponse = $paymentTradeController->show($paymentTrade->refresh());
                $showResponse["status"] = $refundStatus;
                return $showResponse;
            } catch (CancelRefundException $e) {
                $showResponse = $paymentTradeController->show($paymentTrade->refresh());
                $showResponse["result"] = false;
                return $showResponse;
            } catch (\Throwable $e) {
                $showResponse = $paymentTradeController->show($paymentTrade->refresh());
                $showResponse["result"] = true;
                $showResponse["status"] = 0;
                return $showResponse;
            }
        } else {
            DB::transaction(function () use ($request, $paymentTrade, $fee) {
                $refundTrade = $paymentTrade->refundPrepare($fee, false);
                $paymentTrade->refundPreparationCommit($refundTrade, $request->transactionId);
            });
            $showResponse = $paymentTradeController->show($paymentTrade->refresh());
            $showResponse["status"] = 1;
            return $showResponse;
        }
    }

    public function commit($paymentTradeRefund)
    {
        DB::beginTransaction();
        try {
            /**
             * @var PaymentTradeRefund $paymentTradeRefund
             */
            $paymentTradeRefund = PaymentTradeRefund::query()->where("id", $paymentTradeRefund)->lockForUpdate()->first();
            switch ($paymentTradeRefund->status) {
                case TradeRefundStatus::STATUS_PENDING:
                    $paymentTradeRefund->trade->refundPreparationCommit($paymentTradeRefund);
                    break;
                default:
                    $returnValue = ["result" => false, "message" => "Invalid status", "errno" => GlobalErrorCode::INVALID_PAYMENT_TRADE_STATUS];
                    goto ROLLBACK;
            }

            DB::commit();
            $paymentTradeRefund->trade->user->autoChangeUserQuota();
            $paymentTradeController = new PaymentTradeController();
            return $paymentTradeController->show($paymentTradeRefund->trade->refresh());
            ROLLBACK:
            DB::rollback();
            return $returnValue;
        } catch (\Throwable $throwable) {
            DB::rollback();
            throw $throwable;
        }
    }

    public function cancel($paymentTradeRefund)
    {
        DB::beginTransaction();
        try {
            /**
             * @var PaymentTradeRefund $paymentTradeRefund
             */
            $paymentTradeRefund = PaymentTradeRefund::query()->where("id", $paymentTradeRefund)->lockForUpdate()->first();
            switch ($paymentTradeRefund->status) {
                case TradeRefundStatus::STATUS_SUCCEED:
                    $paymentTradeRefund->refundRollback();
                    break;
                case TradeRefundStatus::STATUS_PENDING:
                    $paymentTradeRefund->trade->refundPreparationRollback($paymentTradeRefund, TradeRefundStatus::STATUS_CANCELLED);
                    break;
                default:
                    $returnValue = ["result" => false, "message" => "Invalid status", "errno" => GlobalErrorCode::INVALID_PAYMENT_TRADE_STATUS];
                    goto ROLLBACK;
            }

            DB::commit();
            $paymentTradeRefund->trade->user->autoChangeUserQuota();
            $paymentTradeController = new PaymentTradeController();
            return $paymentTradeController->show($paymentTradeRefund->trade->refresh());
            ROLLBACK:
            DB::rollback();
            return $returnValue;
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function changeStatus(Request $request, $paymentTradeRefund)
    {
        $this->validate($request, [
            "status" => [
                "required",
                Rule::in(TradeRefundStatus::STATUS_CANCELLED, TradeRefundStatus::STATUS_FAILED),
            ]
        ]);
        DB::beginTransaction();
        try {
            /**
             * @var PaymentTradeRefund $paymentTradeRefund
             */
            $paymentTradeRefund = PaymentTradeRefund::query()->where("id", $paymentTradeRefund)->lockForUpdate()->first();
            if ($paymentTradeRefund->status !== TradeRefundStatus::STATUS_CANCELLED && $paymentTradeRefund->status !== TradeRefundStatus::STATUS_FAILED) {
                $returnValue = ["result" => false, "message" => "Invalid status", "errno" => GlobalErrorCode::INVALID_PAYMENT_TRADE_STATUS];
                goto ROLLBACK;
            }
            $paymentTradeRefund->update(["status" => $request->status]);

            DB::commit();
            $paymentTradeController = new PaymentTradeController();
            return $paymentTradeController->show($paymentTradeRefund->trade->refresh());
            ROLLBACK:
            DB::rollback();
            return $returnValue;
        } catch (\Throwable $e) {
            DB::rollback();
            throw $e;
        }
    }

    private function validatePaymentModuleStatus(PaymentTrade $paymentTrade)
    {
        /**
         * @var PaymentModule $paymentModule
         */
        $paymentModule = $paymentTrade->paymentModule;
        if (!$paymentModule)
            return false;

        if ($paymentModule->status !== ModuleStatus::STATUS_ENABLED)
            return false;

        return true;
    }

    /**
     * @param Request $request
     * @param $fee
     * @param PaymentTrade $paymentTrade
     * @param bool $negativeCreditValidate
     * @return array|int
     * @throws CancelRefundException
     * @throws \Throwable
     */
    private function refundViaPaymentModule(Request $request, $fee, PaymentTrade $paymentTrade, $negativeCreditValidate = true)
    {
        /**
         * @var PaymentModule $paymentModule
         */
        $paymentModule = $paymentTrade->paymentModule;
        $basicPaymentModule = $paymentModule->getBasicPaymentModule();

        /**
         * @var PaymentTradeRefund $refundTrade
         */
        $refundTrade = $paymentTrade->refundPrepare($fee, $negativeCreditValidate);

        if ($basicPaymentModule instanceof RefundNotifiable) {
            $notifyURL = route("paymentModules.refundNotify", [$paymentModule->id, $refundTrade->no]);
        } else {
            $notifyURL = route("paymentModules.notify", [$paymentModule->id, $refundTrade->no]);
        }

        try {
            $refundResult = $basicPaymentModule->refund($request, $refundTrade, $notifyURL);
            $refundStatus = 0;
            if ($refundResult === true || is_string($refundResult)) {
                $refundStatus = 1;
                $paymentTrade->refundPreparationCommit($refundTrade, $refundResult);
            }
            return $refundStatus;
        } catch (CancelRefundException $e) {
            $paymentTrade->refundPreparationRollback($refundTrade);
            Common::logException($e);
            throw $e;
        } catch (\Throwable $throwable) {
            Common::logException($throwable);
            throw $throwable;
        }
    }
}
