<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-2
 * Time: 下午7:36
 */

namespace App\IPPool;


use App\User;
use Illuminate\Support\Facades\DB;

trait IPAssignment
{
    use ChargeableAssignment;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function calculateTotalHourlyPrice()
    {
        $ipVersion = self::VERSION;
        return DB::select(<<<EOF
SELECT IFNULL(SUM(IFNULL(ipv{$ipVersion}_assignments.override_price_per_hour, ipv{$ipVersion}_pools.price_per_hour)), 0) AS total_price_per_hour
FROM ipv{$ipVersion}_assignments
       INNER JOIN ipv{$ipVersion}_pools on ipv{$ipVersion}_assignments.pool_id = ipv{$ipVersion}_pools.id
WHERE user_id IS NOT NULL
  AND unbindable != 0;
EOF
        )[0]->total_price_per_hour;
    }
}