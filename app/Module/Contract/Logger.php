<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-8
 * Time: 下午3:27
 */

namespace App\Module\Contract;


use App\Module\LogLevel;

interface Logger
{
    /**
     * @param mixed $content
     * @param string $identify
     * @param int $type
     * @param int $level
     * @return void
     */
    public function log($content, $identify = null, $type = null, $level = LogLevel::LEVEL_DEBUG);
}