<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-8
 * Time: 下午3:28
 */

namespace App\Module;


interface LogLevel
{
    const LEVEL_DEBUG = 0;
    const LEVEL_INFO = 1;
    const LEVEL_WARNING = 2;
    const LEVEL_ERROR = 3;
}