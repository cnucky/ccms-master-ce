<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-5
 * Time: 下午7:46
 */

namespace App\Module\Exception;

/**
 * Class CancelRefundException
 * If refund can be cancelled, throw this exception
 * @package App\Module\Exception
 */
class CancelRefundException extends PaymentModuleException
{
}