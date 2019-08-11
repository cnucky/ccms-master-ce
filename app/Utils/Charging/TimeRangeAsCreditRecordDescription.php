<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-4
 * Time: 上午1:18
 */

namespace App\Utils\Charging;


trait TimeRangeAsCreditRecordDescription
{
    public function getCreditRecordDescription($lastChargeTime, $currentChargingTime, $chargeTimeInSecond)
    {
        return sprintf("%s - %s (%s seconds)", $lastChargeTime, $currentChargingTime, $chargeTimeInSecond);
    }
}