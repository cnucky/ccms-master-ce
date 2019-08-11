<?php

namespace App;

use App\ChargeHistory\TrafficShareGroupChargeHistory;
use Illuminate\Database\Eloquent\Model;

class TrafficShareGroup extends Model
{
    protected $guarded = [
        "id",
    ];

    public function zones()
    {
        return $this->hasMany(Zone::class, "traffic_share_group_id");
    }

    public function chargeHistories()
    {
        return $this->hasMany(TrafficShareGroupChargeHistory::class, "traffic_share_group_id");
    }

    /**
     * @param null|int $groupId
     * @return string
     */
    public static function txUnitPrice($groupId)
    {
        if (is_null($groupId))
            return self::query()->find(1)->price_per_tx_gib;
        return self::query()->find($groupId)->price_per_tx_gib;
    }
}
