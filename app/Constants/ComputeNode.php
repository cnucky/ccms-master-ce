<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-13
 * Time: 下午1:35
 */

namespace App\Constants;


class ComputeNode
{
    public static function reservedMemoryCapacity()
    {
        return 2048;
    }

    public static function reservedDiskCapacity()
    {
        return 4;
    }
}