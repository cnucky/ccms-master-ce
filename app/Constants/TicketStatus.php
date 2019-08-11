<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-10
 * Time: 下午6:47
 */

namespace App\Constants;


interface TicketStatus
{
    const STATUS_PENDING = 0;
    const STATUS_ANSWERED = 1;
    const STATUS_CUSTOMER_REPLIED = 2;
    const STATUS_ON_HOLD = 3;
    const STATUS_IN_PROGRESS = 5;
    const STATUS_CLOSED = 6;
}