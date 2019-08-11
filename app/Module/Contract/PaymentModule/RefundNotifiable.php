<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-9
 * Time: 下午2:35
 */

namespace App\Module\Contract\PaymentModule;


use App\Module\RefundNotifyResult;
use Illuminate\Http\Request;

interface RefundNotifiable
{
    /**
     * @param Request $request
     * @return RefundNotifyResult
     */
    public function refundNotify(Request $request) : RefundNotifyResult;
}