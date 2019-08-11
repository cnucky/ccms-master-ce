<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-5
 * Time: 下午7:11
 */

namespace App\Module\Resource\Payment;


use App\Module\Constants\Payment\ValidationResult;

class NotifyResult
{
    public $type;

    public $no;

    public $fee;

    public $transactionId;

    public $view;

    public $result;

    public function __construct($type, $result = ValidationResult::INCORRECT_SIGNATURE, $no = null, $fee = null, $transactionId = null, $view = "INCORRECT_SIGNATURE")
    {
        $this->type = $type;
        $this->result = $result;
        $this->no = $no;
        $this->fee = $fee;
        $this->transactionId = $transactionId;
        $this->view = $view;
    }

    public function withId($id)
    {
        $this->no = $id;
        return $this;
    }

    public function withFee($fee)
    {
        $this->fee = $fee;
        return $this;
    }

    public function withTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function withView($view)
    {
        $this->view = $view;
        return $this;
    }

    public function withResult($result)
    {
        $this->result = $result;
        return $this;
    }
}