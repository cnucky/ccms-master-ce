<?php

namespace App;

use App\Module\Constants\Payment\TradeStatus;
use App\Module\Exception\ZeroFeeException;
use App\Module\PaymentModuleLoader;
use App\Utils\Common;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaymentModule extends Model
{
    protected $guarded = [];

    public function currency()
    {
        return $this->belongsTo(Currency::class, "currency_id");
    }

    public function settings()
    {
        return $this->hasMany(PaymentModuleSetting::class, "payment_module_id");
    }

    public function getPaymentModuleLoader()
    {
        return new PaymentModuleLoader($this->basic_payment_module);
    }

    public function getBasicPaymentModule()
    {
        return $this->getPaymentModuleLoader()->newInstance($this);
    }

    public function getCurrencyCode()
    {
        if ($this->currency)
            return $this->currency->code;
        return self::getDefaultCurrency()->code;
    }

    public function convert2ModuleCurrency($fee)
    {
        if (is_null($this->currency))
            return $fee;
        if ($this->currency->id === self::getDefaultCurrency()->id)
            return $fee;
        $convertedFee = DB::select("SELECT CAST(CAST(? AS DECIMAL(11,2)) * CAST(? AS DECIMAL(12, 6)) AS DECIMAL(11, 2)) AS converted_fee", [$fee, $this->currency->exchange_rate])[0]->converted_fee;
        if ($convertedFee === "0.00")
            throw new ZeroFeeException();
        return $convertedFee;
    }

    public function newTrade($userId, $channel, $fee)
    {
        return PaymentTrade::query()->create([
            "no" => Common::generateTradeNo(),
            "payment_module_id" => $this->id,
            "user_id" => $userId,
            "fee_in_default_currency" => $fee,
            "fee" => $this->convert2ModuleCurrency($fee),
            "basic_payment_module" => $this->basic_payment_module,
            "channel" => $channel,
            "status" => TradeStatus::STATUS_UNPAID,
        ]);
    }

    public static function getDefaultCurrency()
    {
        return Currency::query()->findOrFail(1);
    }
}
