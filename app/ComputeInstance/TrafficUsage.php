<?php

namespace App\ComputeInstance;

use App\Utils\Charging\ClearCharging;
use App\Utils\Charging\Common;
use App\Utils\Charging\FixNonExistsOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use YunInternet\CCMSCommon\Constants\NetworkType;
use YunInternet\CCMSCommon\Model\CompositeKey;

class TrafficUsage extends Model
{
    use CompositeKey;

    use FixNonExistsOwner;

    use ClearCharging;

    public $incrementing = false;

    public $timestamps = false;

    protected $table = "compute_instance_traffic_usages";

    protected $primaryKey = [
        "network_interface_id",
        "id",
    ];

    protected $guarded = [];

    public static function prepareChargeablePublicNetworkTraffics($userId)
    {
        return self::query()
            ->whereNull("last_charged_at")
            ->where("user_id", $userId)
            ->where("network_type", NetworkType::TYPE_PUBLIC_NETWORK)
            // ->where("microtime", "<", strtotime($maxLastChargedTime))
            ->update(Common::chargingItemValues())
            ;
    }

    public static function sumUnchargedPublicNetworkTraffics($userId)
    {
        return DB::table("compute_instance_traffic_usages")
            ->select(DB::raw("FROM_UNIXTIME(microtime, '%Y-%m') AS month, traffic_share_group_id, SUM(rx_byte_count) AS total_rx_bytes, SUM(tx_byte_count) AS total_tx_bytes"))
            ->where("user_id", $userId)
            ->where("network_type", NetworkType::TYPE_PUBLIC_NETWORK)
            ->where("charging_by", Common::connectionIDRawQuery())
            ->whereNull("last_charged_at")
            ->groupBy("month", "traffic_share_group_id")
            ->get()
            ;

        // return DB::select("SELECT FROM_UNIXTIME(microtime, '%Y-%m') AS month, traffic_share_group_id, SUM(rx_byte_count) AS total_rx_bytes, SUM(tx_byte_count) AS total_tx_bytes FROM compute_instance_traffic_usages WHERE user_id = ? AND network_type = ? AND charging_by = CONNECTION_ID() AND last_charged_at IS NULL GROUP BY month, traffic_share_group_id", [$userId, NetworkType::TYPE_PUBLIC_NETWORK]);
    }

    public static function sumChargedPublicNetworkTraffics($userId, $trafficShareGroupId, $month,  &$chargedRXBytes, &$chargedTXBytes)
    {
        $minUnixTimestamp = strtotime($month);

        list($year, $month) = explode("-", $month);
        $year = intval($year);
        $month = intval($month);
        if ($month === 12) {
            $month = 1;
            ++$year;
        } else {
            ++$month;
        }

        $maxUnixTimestamp = strtotime($year . "-" . $month);

        $row = DB::table("compute_instance_traffic_usages")
            ->select(DB::raw("SUM(rx_byte_count) AS total_rx_bytes, SUM(tx_byte_count) AS total_tx_bytes"))
            ->where("user_id", $userId)
            ->where("traffic_share_group_id", $trafficShareGroupId)
            ->where("network_type", NetworkType::TYPE_PUBLIC_NETWORK)
            ->where("charging_by", Common::connectionIDRawQuery())
            ->where("microtime", ">=", $minUnixTimestamp)
            ->where("microtime", "<", $maxUnixTimestamp)
            ->whereNotNull("last_charged_at")
            ->first()
            ;

        $chargedRXBytes = 0;
        $chargedTXBytes = 0;
        if ($row->total_rx_bytes) {
            $chargedRXBytes = $row->total_rx_bytes;
        }
        if ($row->total_tx_bytes) {
            $chargedTXBytes = $row->total_tx_bytes;
        }
        return $row;

        // return DB::select("SELECT traffic_share_group_id, SUM(rx_byte_count) AS total_rx_bytes, SUM(tx_byte_count) AS total_tx_bytes FROM compute_instance_traffic_usages WHERE user_id = ? AND network_type = ? AND charging_by = CONNECTION_ID() AND last_charged_at IS NOT NULL GROUP BY traffic_share_group_id", [$userId, NetworkType::TYPE_PUBLIC_NETWORK]);
    }

    public static function finishCharging($userId, $trafficShareGroupId)
    {
        return self::query()
            ->where("user_id", $userId)
            ->where("traffic_share_group_id", $trafficShareGroupId)
            ->where("charging_by", Common::connectionIDRawQuery())
            ->update([
                "charging_by" => null,
                "last_charged_at" => date("Y-m-d H:i:s"),
                "charging_started_at" => null,
            ])
            ;
    }
}
