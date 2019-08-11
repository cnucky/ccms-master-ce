<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-9
 * Time: 下午2:32
 */

namespace App\Module\Contract\PaymentModule;


use App\Module\Resource\Payment\NotifyResult;
use Illuminate\Http\Request;

interface Notifiable
{
    public function notify(Request $request) : NotifyResult;
}