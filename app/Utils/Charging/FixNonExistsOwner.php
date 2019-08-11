<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-2
 * Time: 下午9:18
 */

namespace App\Utils\Charging;


trait FixNonExistsOwner
{
    /**
     * @return int
     */
    public static function fixChargingByNonExistsOwner()
    {
        return self::query()
            ->whereNotNull("charging_by")
            ->whereRaw("charging_by NOT IN (SELECT id FROM INFORMATION_SCHEMA.PROCESSLIST)")
            ->update([
                "charging_by" => null,
            ]);
    }
}