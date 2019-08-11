<?php

namespace App;

use App\Constants\CreditRecordType;
use App\Exceptions\NegativeCreditOperationException;
use App\Exceptions\NoSuperAdministratorException;
use App\Module\Constants\Payment\TradeRefundStatus;
use App\Module\Constants\Payment\TradeStatus;
use App\Utils\Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class PaymentTrade extends Model
{
    protected $guarded = [];

    protected $hidden = [
        "remark",
        "payment_module_data",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function refunds()
    {
        return $this->hasMany(PaymentTradeRefund::class, "trade_id");
    }

    public function paymentModule()
    {
        return $this->belongsTo(PaymentModule::class, "payment_module_id");
    }

    public function paid($transactionId)
    {
        DB::transaction(function () use (&$transactionId, &$updatedRecordCount) {
            $values = [
                "refundable_fee" => DB::raw("fee_in_default_currency"),
                "status" => TradeStatus::STATUS_PAID,
                "paid_at" => date("Y-m-d H:i:s"),
            ];
            if (!empty($transactionId)) {
                $values["transaction_id"] = $transactionId;
            }

            $updatedRecordCount = self::query()
                ->where("id", $this->id)
                ->where("status", TradeStatus::STATUS_UNPAID)
                ->update($values)
            ;
            if ($updatedRecordCount <= 0) {
                return;
            }

            UserCreditRecord::query()->create([
                "user_id" => $this->user_id,
                "amount" => $this->fee_in_default_currency,
                "type" => CreditRecordType::TYPE_ADD_CREDIT_VIA_PAYMENT_PLATFORM,
                "relative_object_id" => $this->id,
                "description" => $this->basic_payment_module . ($transactionId ? (": " . $transactionId) : ''),
            ]);

            /**
             * @var User $user
             */
            $user = $this->user;
            $user->addCredit($this->fee_in_default_currency);
        });

        return $updatedRecordCount;
    }

    public function removeRefundableFee($fee)
    {
        if (!DB::update("UPDATE payment_trades SET refundable_fee = refundable_fee - ? WHERE id = ?", [$fee, $this->id]))
            throw new ModelNotFoundException();
    }

    public function addRefundableFee($fee)
    {
        if (!DB::update("UPDATE payment_trades SET refundable_fee = refundable_fee + ? WHERE id = ? AND refundable_fee + ? <= fee_in_default_currency", [$fee, $this->id, $fee]))
            throw new ModelNotFoundException();
    }

    public function refundPrepare($fee, $negativeCreditValidate = true) : PaymentTradeRefund
    {
        DB::transaction(function () use ($fee, $negativeCreditValidate, &$refundTrade) {
            $this->removeRefundableFee($fee);
            /**
             * @var User $user
             */
            $user = $this->user;
            // $user->frozeCredit($fee);
            $user->removeCredit($fee);
            if ($negativeCreditValidate && $user->refresh()->credit[0] === "-") {
                throw new NegativeCreditOperationException();
            }

            $refundTrade = PaymentTradeRefund::query()->create([
                "trade_id" => $this->id,
                "no" => Common::generateTradeRefundNo(),
                "fee_in_default_currency" => $fee,
                "fee" => $this->paymentModule->convert2ModuleCurrency($fee),
                "status" => TradeRefundStatus::STATUS_PENDING,
            ]);
            UserCreditRecord::query()->create([
                "user_id" => $this->user_id,
                "amount" => "-" . $fee,
                "type" => CreditRecordType::TYPE_REFUND_VIA_PAYMENT_PLATFORM,
                "relative_object_id" => $refundTrade->id,
                "description" => "Refund #" . $refundTrade->id,
            ]);
        });

        return $refundTrade;
    }

    public function refundPreparationCommit(PaymentTradeRefund $paymentTradeRefund, $transactionId = null)
    {
        /**
         * @var User $user
         */
        $user = $this->user;
        $values = [
            "status" => TradeRefundStatus::STATUS_SUCCEED,
            "refunded_at" => date("Y-m-d H:i:s"),
        ];
        if (is_string($transactionId) && $transactionId !== "") {
            $values["transaction_id"] = $transactionId;
        }

        DB::transaction(function () use ($user, $paymentTradeRefund, &$values) {
            // Only commit for pending trade refund
            if (PaymentTradeRefund::query()
                ->where("id", $paymentTradeRefund->id)
                ->where("status", TradeRefundStatus::STATUS_PENDING)
                ->update($values)
            ) {
                /*
                $user->removeFrozenCredit($paymentTradeRefund->fee_in_default_currency);
                UserCreditRecord::query()->create([
                    "user_id" => $this->user_id,
                    "amount" => "-" . $paymentTradeRefund->fee_in_default_currency,
                    "type" => CreditRecordType::TYPE_REFUND_VIA_PAYMENT_PLATFORM,
                    "relative_object_id" => $paymentTradeRefund->id,
                    "description" => "Refund",
                ]);
                */
            }
        });
    }

    public function refundPreparationRollback(PaymentTradeRefund $paymentTradeRefund, $status = TradeRefundStatus::STATUS_FAILED)
    {
        DB::transaction(function () use ($paymentTradeRefund, $status) {
            // Only rollback for pending trade refund
            if (PaymentTradeRefund::query()
                ->where("id", $paymentTradeRefund->id)
                ->where("status", TradeRefundStatus::STATUS_PENDING)
                ->update([
                    "status" => $status,
                ])
            ) {
                $fee = $paymentTradeRefund->fee_in_default_currency;
                $this->addRefundableFee($fee);
                /**
                 * @var User $user
                 */
                $user = $this->user;
                $user->addCredit($fee);
                UserCreditRecord::query()->create([
                    "user_id" => $this->user_id,
                    "amount" => $fee,
                    "type" => CreditRecordType::TYPE_REFUND_CANCELLATION,
                    "relative_object_id" => $paymentTradeRefund->id,
                    "description" => "Refund #" . $paymentTradeRefund->id . " cancellation.",
                ]);
            }
        });
    }

    public static function statistics($dateFormat, $start, $end)
    {
        return DB::table("payment_trades")
            ->selectRaw("DATE_FORMAT(created_at, '". $dateFormat ."') AS time, SUM(fee_in_default_currency) AS total_fee")
            ->where("created_at", ">=", $start)
            ->where("created_at", "<", $end)
            ->where("status", TradeStatus::STATUS_PAID)
            ->groupBy("time")
            ;
    }
}
