<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-9
 * Time: 下午2:33
 */

namespace App\Module\Contract\PaymentModule;


use App\Module\Exception\ModuleException;
use App\Module\PayNotifyResult;
use Illuminate\Http\Request;

interface PayNotifiable
{
    /**
     * @param Request $request
     * @return PayNotifyResult
     * @throws ModuleException
     */
    public function payNotify(Request $request) : PayNotifyResult;
}