<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-16
 * Time: 上午12:38
 */

namespace App\Http\Controllers\IPPool;


use App\Constants\GlobalErrorCode;
use App\IPPool\IPv4;
use App\IPPool\IPv6;
use Illuminate\Support\Facades\DB;

trait IPPool
{
    /**
     * @param IPv4|IPv6 $pool
     * @param int $distance
     * @return true|array
     */
    public function isLastUsableIPBeforeAnyAssignedIP($pool, $distance)
    {
        DB::transaction(function () use ($pool, $distance, &$result) {
            $result = true;
            // Where on unique column, only lock the row which position >= $distance
            $pool->assigned()->where("position", ">=", $distance)->lockForUpdate();
            $lastAssignedCount = $pool->assigned()->where("position", ">=", $distance)->whereNotNull("user_id")->count();
            if ($lastAssignedCount) {
                $result = ["result" => false, "errno" => GlobalErrorCode::LAST_IP_SMALLER_THAN_ASSIGNED_IP, "count" => $lastAssignedCount];
                return;
            }
            $pool->assigned()->where("position", ">=", $distance)->delete();
        });

        return $result;
    }
}