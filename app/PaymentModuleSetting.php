<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentModuleSetting extends Model
{
    protected $guarded = [];

    public function paymentModule()
    {
        return $this->belongsTo(PaymentModule::class, "payment_module_id");
    }
}
