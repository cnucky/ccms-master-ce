<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-5
 * Time: 下午9:17
 */

namespace App\Module\Constants\Payment;


interface TradeStatus
{
    const STATUS_UNPAID = 0;
    const STATUS_PAID = 1;
    const STATUS_CANCELLED = 2;
}