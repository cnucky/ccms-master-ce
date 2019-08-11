<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-8
 * Time: 下午8:05
 */

namespace App\Module\Payment\PayPal;


interface Constants
{
    const TYPE_ORDER_CREATE_RESPONSE = 1;
    const TYPE_ORDER_CAPTURE_RESPONSE = 2;
    const TYPE_REFUND_CAPTURE_RESPONSE = 3;
    const TYPE_WEBHOOK_SIGNATURE_VERIFY_RESPONSE = 5;
    const TYPE_REFUND_HTTP_ERROR = 6;
}