<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-5
 * Time: 下午4:07
 */

namespace App\Module\Constants\Payment;


interface PayResponseType
{
    const TYPE_FORM_FIELDS = 1;
    const TYPE_URL = 2;
    const TYPE_QR_CODE = 3;
}