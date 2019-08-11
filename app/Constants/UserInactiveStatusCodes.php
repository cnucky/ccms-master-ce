<?php
/**
 * Created by PhpStorm.
 * Date: 19-5-5
 * Time: 下午2:05
 */

namespace App\Constants;


interface UserInactiveStatusCodes
{
    const STATUS_NORMAL = 0;
    const STATUS_PENDING = 1;
    const STATUS_INACTIVE = 2;
}