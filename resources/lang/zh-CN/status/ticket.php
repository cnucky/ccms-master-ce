<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-10
 * Time: 下午7:37
 */

return [
    \App\Constants\TicketStatus::STATUS_PENDING => "待回复",
    \App\Constants\TicketStatus::STATUS_ANSWERED => "已回复",
    \App\Constants\TicketStatus::STATUS_CUSTOMER_REPLIED => "客户已回复",
    \App\Constants\TicketStatus::STATUS_ON_HOLD => "已受理",
    \App\Constants\TicketStatus::STATUS_IN_PROGRESS => "处理中",
    \App\Constants\TicketStatus::STATUS_CLOSED => "已完成",
];