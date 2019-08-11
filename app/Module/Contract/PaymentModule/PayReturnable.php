<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-7
 * Time: 下午5:55
 */

namespace App\Module\Contract\PaymentModule;


use App\Module\Resource\Payment\PayReturnResult;
use Illuminate\Http\Request;

interface PayReturnable
{
    public function payReturn(Request $request) : PayReturnResult;
}