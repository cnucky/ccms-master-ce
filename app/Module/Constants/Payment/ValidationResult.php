<?php
namespace App\Module\Constants\Payment;


interface ValidationResult
{
    const CORRECT_SIGNATURE = 126; // 验签通过，系统按照正常流程进行处理
    const CORRECT_SIGNATURE_AND_RETURN = 1; // 验签通过，但系统无需进行任何额外操作

    const INCORRECT_SIGNATURE = 2; // 错误的签名
    const INCORRECT_SOURCE_IP = 3; // 错误的来源IP

    const RETURN_DIRECTLY = 5; // 无需执行任何操作
}