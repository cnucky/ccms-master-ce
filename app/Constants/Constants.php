<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-13
 * Time: 下午10:03
 */

namespace App\Constants;


interface Constants
{
    const DECIMAL_8_4_PRICE_VALIDATE_RULE = 'nullable|regex:/^(\d){0,4}(\.(\d){0,4}){0,1}$/';

    const DECIMAL_13_4_PRICE_VALIDATE_RULE = 'regex:/^(\d){0,9}(\.(\d){0,4}){0,1}$/';
}