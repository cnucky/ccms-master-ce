<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-18
 * Time: 下午8:18
 */

namespace App\Constants\Volume\OperationRequest;


interface TypeCode
{
    const TYPE_RESIZE = 0;
    const TYPE_NEW = 1;
    const TYPE_DETACH = 2;
    const TYPE_RELEASE = 3;
    const TYPE_ATTACH = 5;
}