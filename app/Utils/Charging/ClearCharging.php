<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-3
 * Time: 下午8:31
 */

namespace App\Utils\Charging;


trait ClearCharging
{
    public static function clearCharging()
    {
        return self::query()
            ->where("charging_by", Common::connectionIDRawQuery())
            ->update([
                "charging_by" => null,
                "charging_started_at" => null,
            ]);
    }
}