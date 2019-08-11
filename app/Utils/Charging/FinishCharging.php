<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-2
 * Time: 下午9:24
 */

namespace App\Utils\Charging;


trait FinishCharging
{
    public function finishCharging($finishedAt): int
    {
        return self::query()
            ->where("charging_by", Common::connectionIDRawQuery())
            ->update([
                "charging_by" => null,
                "last_charged_at" => $finishedAt,
                "charging_started_at" => null,
            ]);
    }
}