<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-19
 * Time: 下午9:58
 */

namespace App\Constants\Volume;


use App\Utils\Volume\Constants;

interface StatusCode
{
    const STATUS_NORMAL = Constants::STATUS_NORMAL;
    const STATUS_CREATING = Constants::STATUS_CREATING;
    const STATUS_ATTACHING = 6;
    const STATUS_DETACHING = 8;
}