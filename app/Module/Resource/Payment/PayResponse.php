<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-5
 * Time: 下午4:06
 */

namespace App\Module\Resource\Payment;


use App\Module\Constants\Payment\PayResponseType;

class PayResponse
{
    public $type;

    public $response;

    public function __construct($type, $response)
    {
        $this->type = $type;
        $this->response = $response;
    }

    public static function formFieldResponse($formFields)
    {
        return new self(PayResponseType::TYPE_FORM_FIELDS, $formFields);
    }

    public static function urlResponse($url)
    {
        return new self(PayResponseType::TYPE_URL, $url);
    }

    public static function qrCodeResponse($qrCodeData)
    {
        return new self(PayResponseType::TYPE_QR_CODE, $qrCodeData);
    }
}