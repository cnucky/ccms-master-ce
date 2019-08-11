<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-9
 * Time: 下午2:50
 */

namespace App\Module\Contract\PaymentModule;


interface NotifyType
{
    const TYPE_PAY_NOTIFY = 0;
    const TYPE_REFUND_NOTIFY = 1;
    const TYPE_PAY_RETURN = 2;
}