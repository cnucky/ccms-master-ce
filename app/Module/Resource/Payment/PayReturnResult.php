<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-7
 * Time: 下午7:02
 */

namespace App\Module\Resource\Payment;


use App\Module\Constants\Payment\ValidationResult;
use App\Module\Contract\PaymentModule\NotifyType;

class PayReturnResult extends NotifyResult
{
    public function __construct($result = ValidationResult::INCORRECT_SIGNATURE, $no = null, $fee = null, $transactionId = null, $view = "INCORRECT_SIGNATURE")
    {
        parent::__construct(NotifyType::TYPE_PAY_RETURN, $result, $no, $fee, $transactionId, $view);
    }
}