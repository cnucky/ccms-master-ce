<?php

namespace App\Http\Controllers\PaymentTrade;

use App\Constants\GlobalErrorCode;
use App\Module\Constants\Payment\TradeRefundStatus;
use App\Module\Constants\Payment\TradeStatus;
use App\Module\Exception\CancelRefundException;
use App\Module\Exception\ModuleException;
use App\Module\Exception\ModuleNotFoundException;
use App\PaymentModule;
use App\PaymentTrade;
use App\PaymentTradeRefund;
use App\User;
use App\Utils\Common;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PaymentTradeController extends Controller
{
    public function show(PaymentTrade $paymentTrade)
    {
        return [
            "result" => true,
            "data" => [
                "paymentTrade" => $paymentTrade,
                "user" => $paymentTrade->user,
                "module" => $paymentTrade->paymentModule,
                "refunds" => $paymentTrade->refunds,
                "totalRefundedFee" => $paymentTrade->refunds()->where("status", TradeRefundStatus::STATUS_SUCCEED)->sum("fee_in_default_currency"),
                "totalRefundingFee" => $paymentTrade->refunds()->where("status", TradeRefundStatus::STATUS_PENDING)->sum("fee_in_default_currency"),
            ],
        ];
    }

    public function addTransaction(Request $request, PaymentTrade $paymentTrade)
    {
        if ($paymentTrade->status !== TradeStatus::STATUS_UNPAID) {
            return ["result" => false, "message" => "Invalid payment trade status", "errno" => GlobalErrorCode::INVALID_PAYMENT_TRADE_STATUS];
        }

        $this->validate($request, [
            "transactionId" => "nullable|max:191",
        ]);

        DB::transaction(function () use ($request, $paymentTrade) {
            if ($paymentTrade->paid($request->transactionId)) {
                if (intval($request->type) === 0) {
                    $paymentTrade->update([
                        "refundable_fee" => "0",
                    ]);
                }
            }
        });

        return $this->show($paymentTrade->refresh());
    }

    public function removeTransaction(Request $request, PaymentTrade $paymentTrade)
    {
        if ($paymentTrade->refunds()->whereNotIn("status", [TradeRefundStatus::STATUS_CANCELLED, TradeRefundStatus::STATUS_FAILED])->count()) {
            return ["result" => false, "message" => "Refund exists", "errno" => GlobalErrorCode::REFUND_EXISTS];
        }

        DB::transaction(function () use ($paymentTrade) {
            if (PaymentTrade::query()
                ->where("id", $paymentTrade->id)
                ->where("status", TradeStatus::STATUS_PAID)
                ->update([
                "status" => TradeStatus::STATUS_CANCELLED,
                "refundable_fee" => "0",
                "transaction_id" => null,
            ])) {
                /**
                 * @var User $user
                 */
                $user = $paymentTrade->user;
                $user->removeCredit($paymentTrade->fee_in_default_currency);
            }
        });

        return $this->show($paymentTrade->refresh());
    }

    public function changeStatus(Request $request, PaymentTrade $paymentTrade)
    {
        $this->validate($request, [
            "status" => [
                "required",
                Rule::in([TradeStatus::STATUS_UNPAID, TradeStatus::STATUS_PAID, TradeStatus::STATUS_CANCELLED]),
            ],
        ]);
        $paymentTrade->update(["status" => $request->status]);
        return $this->show($paymentTrade);
    }

    public function tradeStatus(Request $request, PaymentTrade $paymentTrade)
    {
        if ($request->user()->id === $paymentTrade->user_id) {
            return ["result" => true, "credit" => $request->user()->credit, "status" => $paymentTrade->status];
        }
        return ["result" => false];
    }
}
