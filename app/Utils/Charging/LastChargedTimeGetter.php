<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-2
 * Time: 下午5:09
 */

namespace App\Utils\Charging;


trait LastChargedTimeGetter
{
    public function getLastChargedTime(): string
    {
        $lastChargedTime = $this->last_charged_at;
        if (is_null($lastChargedTime))
            return $this->created_at;
        return $lastChargedTime;
    }
}