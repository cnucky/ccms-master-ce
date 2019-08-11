<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-8
 * Time: 上午1:35
 */

namespace App\Module\Constants\Payment;


interface TradeRefundStatus
{
    const STATUS_PENDING = 0;
    const STATUS_CANCELLED = 1;
    const STATUS_FAILED = 2;
    const STATUS_SUCCEED = 3;
}