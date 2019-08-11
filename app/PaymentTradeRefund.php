<?php

namespace App;

use App\Constants\CreditRecordType;
use App\Module\Constants\Payment\TradeRefundStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentTradeRefund extends Model
{
    protected $guarded = [];

    public function trade()
    {
        return $this->belongsTo(PaymentTrade::class, "trade_id");
    }

    public function refundRollback()
    {
        DB::transaction(function () {
            if (!self::query()
                ->where("id", $this->id)
                ->where("status", TradeRefundStatus::STATUS_SUCCEED)
                ->update(["status" => TradeRefundStatus::STATUS_CANCELLED])
            ) {
                return;
            }
            $this->refresh();

            /**
             * @var PaymentTrade $paymentTrade
             */
            $paymentTrade = $this->trade;
            $paymentTrade->addRefundableFee($this->fee_in_default_currency);
            /**
             * @var User $user
             */
            $user = $paymentTrade->user;
            UserCreditRecord::query()->create([
                "user_id" => $user->id,
                "amount" => $this->fee_in_default_currency,
                "type" => CreditRecordType::TYPE_REFUND_CANCELLATION,
                "relative_object_id" => $this->id,
                "description" => "Refund #". $this->id ." cancellation",
            ]);
            $user->addCredit($this->fee_in_default_currency);
        });
    }

    public static function statistics($dateFormat, $start, $end)
    {
        return DB::table("payment_trade_refunds")
            ->selectRaw("DATE_FORMAT(created_at, '". $dateFormat ."') AS time, SUM(fee_in_default_currency) AS total_fee")
            ->where("created_at", ">=", $start)
            ->where("created_at", "<", $end)
            ->where("status", TradeRefundStatus::STATUS_SUCCEED)
            ->groupBy("time")
            ;
    }
}
