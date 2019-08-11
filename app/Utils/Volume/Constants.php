<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-18
 * Time: 下午6:10
 */

namespace App\Utils\Volume;


interface Constants
{
    const UNIQUE_ID_PREFIX = "lv-";

    const STATUS_CREATING = 0;
    const STATUS_NORMAL = 1;
    const STATUS_LOCK = 2;

    const STATUS_CREATE_UNSUCCESSFULLY = 3;
    const STATUS_DESTROY_UNSUCCESSFULLY = 5;
}