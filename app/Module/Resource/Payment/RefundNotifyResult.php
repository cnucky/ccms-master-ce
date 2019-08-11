<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-5
 * Time: 下午3:29
 */

namespace App\Module;


use App\Module\Constants\Payment\ValidationResult;
use App\Module\Contract\PaymentModule\NotifyType;
use App\Module\Resource\Payment\NotifyResult;

class RefundNotifyResult extends NotifyResult
{
    public function __construct($result = ValidationResult::INCORRECT_SIGNATURE, $no = null, $fee = null, $transactionId = null, $view = "INCORRECT_SIGNATURE")
    {
        parent::__construct(NotifyType::TYPE_REFUND_NOTIFY, $result, $no, $fee, $transactionId, $view);
    }
}