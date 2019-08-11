<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-4
 * Time: 下午9:17
 */

namespace App\Constants;


interface CommonStatusCode
{
    const AVAILABLE = 0;

    const HIDDEN = 1;

    const DISABLED = 2;

    const AVAILABLE_STATUS_CODES = [
        self::AVAILABLE,
        self::HIDDEN,
        self::DISABLED,
    ];
}