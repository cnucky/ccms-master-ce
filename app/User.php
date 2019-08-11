<?php

namespace App;

use App\ChargeHistory\TrafficShareGroupChargeHistory;
use App\ComputeInstance\LocalVolume;
use App\ComputeInstance\TrafficUsage;
use App\Constants\ComputeInstanceStatusCode;
use App\Constants\CreditRecordType;
use App\Constants\StatusCode;
use App\IPPool\IPv4Assignment;
use App\IPPool\IPv6Assignment;
use App\Module\Constants\Payment\TradeRefundStatus;
use App\Module\Constants\Payment\TradeStatus;
use App\Node\ComputeNode;
use App\Ticket\Ticket;
use App\Utils\Charging\ClearCharging;
use App\Utils\Charging\Common;
use App\Utils\Charging\FinishCharging;
use App\Utils\Charging\FixNonExistsOwner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use YunInternet\CCMSCommon\Constants\NetworkType;

class User extends Authenticatable
{
    use Notifiable;

    use FixNonExistsOwner;

    use FinishCharging;

    use ClearCharging;

    const STATUS_PENDING = StatusCode::STATUS_PENDING;
    const STATUS_NORMAL = StatusCode::STATUS_NORMAL;
    const STATUS_SUSPENDED = StatusCode::STATUS_SUSPENDED;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        "id",
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function instances()
    {
        return $this->hasMany(ComputeInstance::class);
    }

    public function localVolumes()
    {
        return $this->hasMany(LocalVolume::class);
    }

    public function ipv4s()
    {
        return $this->hasMany(IPv4Assignment::class);
    }

    public function ipv6s()
    {
        return $this->hasMany(IPv6Assignment::class);
    }

    public function trafficUsages()
    {
        return $this->hasMany(TrafficUsage::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function paymentTrade()
    {
        return $this->hasMany(PaymentTrade::class);
    }

    public function paymentTradeRefund()
    {
        return $this->hasManyThrough(PaymentTradeRefund::class, PaymentTrade::class, "user_id", "trade_id", "id", "id");
    }

    public function creditRecords()
    {
        return $this->hasMany(UserCreditRecord::class);
    }

    public function userQuota()
    {
        return $this->belongsTo(UserQuota::class);
    }

    public function getNetIncomingAttribute()
    {
        $paid = $this->paymentTrade()->where("status", TradeStatus::STATUS_PAID)->sum("fee_in_default_currency");
        $refunded = $this->paymentTradeRefund()->where("payment_trade_refunds.status", TradeRefundStatus::STATUS_SUCCEED)->sum("payment_trade_refunds.fee_in_default_currency");

        return intval($paid) - intval($refunded);
    }

    public function getTotalChargedAmountAttribute()
    {
        return DB::table("user_credit_records")
            ->selectRaw("ABS(IFNULL(SUM(amount), 0)) AS total_amount")
            ->where("user_id", $this->id)
            ->whereIn("type", CreditRecordType::CONSUMPTION_CREDIT_RECORD_TYPE)
            ->first()
            ->total_amount
            ;
    }

    public function getTotalChargedAmountGroupByTypeAttribute()
    {
        return DB::table("user_credit_records")
            ->selectRaw("type, ABS(IFNULL(SUM(amount), 0)) AS total_amount")
            ->where("user_id", $this->id)
            ->whereIn("type", CreditRecordType::CONSUMPTION_CREDIT_RECORD_TYPE)
            ->groupBy("type")
            ->get()
            ->keyBy("type")
            ;

    }

    public function getCurrentMonthTotalChargedAmountAttribute()
    {
        return DB::table("user_credit_records")
            ->selectRaw("ABS(IFNULL(SUM(amount), 0)) AS total_amount")
            ->where("user_id", $this->id)
            ->whereIn("type", CreditRecordType::CONSUMPTION_CREDIT_RECORD_TYPE)
            ->where("created_at", ">=", date("Y-m-01 00:00:00"))
            ->first()
            ->total_amount
            ;
    }

    public function getUserCurrentMonthTrafficUsageAttribute()
    {
        $minTime = strtotime(date("Y-m"));
        return DB::table("compute_instance_traffic_usages")
            ->selectRaw("network_type, traffic_share_group_id, SUM(rx_byte_count) AS total_rx_byte_count, SUM(tx_byte_count) AS total_tx_byte_count")
            ->where("microtime", ">=", $minTime)
            ->where("user_id", $this->id)
            ->groupBy("network_type", "traffic_share_group_id")
            ->get()
            ;
    }

    public function getUserDailyChargedAmountAttribute($minTime = null)
    {
        if (is_null($minTime)) {
            $minTime = \App\Utils\Common::lastMonth();
        }
        return $this->commonCreditRecordBuilder( "%Y-%m-%d", $minTime)->get();
    }

    public function getUserDailyChargedAmountGroupByTypeAttribute($minTime = null)
    {
        if (is_null($minTime)) {
            $minTime = \App\Utils\Common::lastMonth();
        }
        return $this->commonCreditRecordBuilder( "%Y-%m-%d", $minTime, true)->get();
    }

    public function getUserMonthlyChargedAmountAttribute($minTime = null)
    {
        if (is_null($minTime)) {
            $minTime = \App\Utils\Common::lastYear();
        }
        return $this->commonCreditRecordBuilder( "%Y-%m", $minTime)->get();
    }

    public function getUserMonthlyChargedAmountGroupByTypeAttribute($minTime = null)
    {
        if (is_null($minTime)) {
            $minTime = \App\Utils\Common::lastYear();
        }
        return $this->commonCreditRecordBuilder( "%Y-%m", $minTime, true)->get();
    }

    public function getComputeInstanceTotalPriceAttribute()
    {
        return DB::select(<<<EOF
SELECT IFNULL(SUM(IFNULL(compute_instances.override_price_per_hour, IFNULL(compute_instance_packages.price_per_hour, 0))), 0) AS total_price_per_hour
FROM compute_instances
       LEFT JOIN compute_instance_packages
                 ON compute_instances.compute_instance_package_id = compute_instance_packages.id
WHERE compute_instances.deleted_at IS NULL
  AND compute_instances.user_id = ?
EOF
, [$this->id])[0]->total_price_per_hour;
    }

    public function getLocalVolumeTotalPriceAttribute()
    {
        return DB::select(<<<EOF
SELECT IFNULL(SUM(compute_instance_local_volumes.capacity * IFNULL(compute_instance_local_volumes.override_price_per_hour, zones.storage_price_per_hour_per_gib)), 0) AS total_price_per_hour
FROM ((compute_instance_local_volumes INNER JOIN compute_nodes ON compute_instance_local_volumes.compute_node_id = compute_nodes.id)
       INNER JOIN zones ON compute_nodes.zone_id = zones.id)
WHERE compute_instance_local_volumes.user_id = ?
EOF
, [$this->id])[0]->total_price_per_hour;
    }

    public function getElasticIPv4TotalPriceAttribute()
    {
        return DB::select(<<<EOF
SELECT IFNULL(SUM(IFNULL(ipv4_assignments.override_price_per_hour, ipv4_pools.price_per_hour)), 0) AS total_price_per_hour
FROM ipv4_assignments
       INNER JOIN ipv4_pools on ipv4_assignments.pool_id = ipv4_pools.id
WHERE user_id = ?
  AND unbindable != 0;
EOF
, [$this->id])[0]->total_price_per_hour;
    }

    public function getElasticIPv6TotalPriceAttribute()
    {
        return DB::select(<<<EOF
SELECT IFNULL(SUM(IFNULL(ipv6_assignments.override_price_per_hour, ipv6_pools.price_per_hour)), 0) AS total_price_per_hour
FROM ipv6_assignments
       INNER JOIN ipv6_pools on ipv6_assignments.pool_id = ipv6_pools.id
WHERE user_id = ?
  AND unbindable != 0;
EOF
            , [$this->id])[0]->total_price_per_hour;
    }

    public function autoChangeUserQuota()
    {
        if ($this->disable_quota_auto_upgrade)
            return;
        $netIncoming = $this->getNetIncomingAttribute();
        $userQuota = UserQuota::query()->where("auto_upgrade_after_net_income_more_than", "<=", $netIncoming)->orderByDesc("auto_upgrade_after_net_income_more_than")->limit(1)->first();
        if ($userQuota) {
            $this->update([
                "user_quota_id" => $userQuota->id,
            ]);
        }
    }

    public function addCredit($value)
    {
        if (!DB::update('UPDATE `users` SET credit = credit + CAST(? AS DECIMAL(13, 4)) WHERE id = ?', [$value, $this->id]))
            throw new ModelNotFoundException();
    }

    public function addFrozenCredit($value)
    {
        if (!DB::update('UPDATE `users` SET frozen_credit = frozen_credit + CAST(? AS DECIMAL(13, 4)) WHERE id = ?', [$value, $this->id]))
            throw new ModelNotFoundException();
    }

    public function removeCredit($value)
    {
        if (!DB::update('UPDATE `users` SET credit = credit - CAST(? AS DECIMAL(13, 4)) WHERE id = ?', [$value, $this->id]))
            throw new ModelNotFoundException();
    }

    public function removeFrozenCredit($value)
    {
        if (!DB::update('UPDATE `users` SET frozen_credit = frozen_credit - CAST(? AS DECIMAL(13, 4)) WHERE id = ?', [$value, $this->id]))
            throw new ModelNotFoundException();
    }

    public function frozeCredit($value)
    {
        DB::transaction(function () use ($value) {
            $this->removeCredit($value);
            $this->addFrozenCredit($value);
        });
    }

    public function unfrozeCredit($value)
    {
        DB::transaction(function () use ($value) {
            $this->removeFrozenCredit($value);
            $this->addCredit($value);
        });
    }

    /**
     * @param int|TrafficShareGroup $trafficShareGroup
     * @return null|int
     */
    public function calculateFreeTXTrafficAtShareGroup($trafficShareGroup)
    {
        $trafficShareGroupId = $trafficShareGroup;
        if ($trafficShareGroup instanceof TrafficShareGroup)
            $trafficShareGroupId = $trafficShareGroup->id;

        $zoneIdListAtTrafficShareGroup = Zone::query()->where("traffic_share_group_id", $trafficShareGroupId)->pluck("id");
        $nodeIdListInZoneIdList = ComputeNode::query()->whereIn("zone_id", $zoneIdListAtTrafficShareGroup)->pluck("id");

        /**
         * @var ComputeInstance[] $computeInstancesInNodeIdList
         */
        $computeInstancesInNodeIdList = $this->instances()->whereIn("compute_node_id", $nodeIdListInZoneIdList)->get();

        $totalFreeTraffic = 0;
        foreach ($computeInstancesInNodeIdList as $computeInstance) {
            $freeTXTrafficValue = $computeInstance->getFreeTXTrafficValue();
            // Null or negative value meaning that unmetered
            if (is_null($freeTXTrafficValue) || $freeTXTrafficValue < 0)
                return null;
            $totalFreeTraffic += $freeTXTrafficValue;
        }

        return $totalFreeTraffic;
    }

    public function getUserFreeTXTrafficAtEachShareGroup()
    {
        $results = [];
        foreach (TrafficShareGroup::query()->get() as $trafficShareGroup) {
            $results[$trafficShareGroup->id] = $this->calculateFreeTXTrafficAtShareGroup($trafficShareGroup->id);
        }
        return $results;
    }

    public function chargeTrafficUsages($beforeCharge = null, $afterCharge = null, $onSkipCharge = null)
    {
        $currentMonthFirstSecond = strtotime(date("Y-m"));
        $userId = $this->id;
        TrafficUsage::clearCharging();
        try {
            TrafficUsage::prepareChargeablePublicNetworkTraffics($userId);
            $userTrafficUsages = TrafficUsage::sumUnchargedPublicNetworkTraffics($userId);
            foreach ($userTrafficUsages as $userTrafficUsage) {
                // For each month with traffic share group
                $month = $userTrafficUsage->month;
                $trafficShareGroupId = $userTrafficUsage->traffic_share_group_id;
                $totalUnchargedRXBytes = $userTrafficUsage->total_rx_bytes;
                $totalUnchargedTXBytes = $userTrafficUsage->total_tx_bytes;

                TrafficUsage::sumChargedPublicNetworkTraffics($userId, $trafficShareGroupId, $month, $chargedRXBytes, $chargedTXBytes);

                $userFreeTXTrafficAtShareGroup = $this->calculateFreeTXTrafficAtShareGroup($trafficShareGroupId);
                if (is_null($userFreeTXTrafficAtShareGroup)) {
                    // No free traffic limit, mark as charged directly.
                    TrafficUsage::finishCharging($userId, $trafficShareGroupId);
                    try {
                        if (is_callable($onSkipCharge)) {
                            $onSkipCharge($this, 2, $trafficShareGroupId, $totalUnchargedRXBytes, $totalUnchargedTXBytes, $userFreeTXTrafficAtShareGroup, 0);
                        }
                    } catch (\Throwable $throwable) {
                    }
                    continue;
                }

                $totalTXBytes2Charge = $totalUnchargedTXBytes - $userFreeTXTrafficAtShareGroup;
                $trafficRecordMonthFirstSecond = strtotime($month);
                // Do not char if current month traffic less than 1 GiB
                if ($totalTXBytes2Charge < 1073741824 && $currentMonthFirstSecond === $trafficRecordMonthFirstSecond) {
                    try {
                        if (is_callable($onSkipCharge)) {
                            $onSkipCharge($this, 1, $trafficShareGroupId, $totalUnchargedRXBytes, $totalUnchargedTXBytes, $userFreeTXTrafficAtShareGroup, $totalTXBytes2Charge);
                        }
                    } catch (\Throwable $throwable) {
                    }
                    continue;
                }


                $txUnitPrice = TrafficShareGroup::txUnitPrice($trafficShareGroupId);
                // Calculate total traffic price
                $price2ChargeResult = DB::select("SELECT CAST(CAST(? AS DECIMAL(6, 4)) * (? / 1073741824) AS DECIMAL(13, 4)) as price_2_charge", [$txUnitPrice, $totalTXBytes2Charge]);
                $price2Charge = $price2ChargeResult[0]->price_2_charge;

                if (is_callable($beforeCharge)) {
                    try {
                        $beforeCharge($this, $trafficShareGroupId, $totalUnchargedRXBytes, $totalUnchargedTXBytes, $userFreeTXTrafficAtShareGroup, $totalTXBytes2Charge, $txUnitPrice, 0, $price2Charge);
                    } catch (\Throwable $throwable) {
                    }
                }

                DB::transaction(function () use ($month, $price2Charge, $userId, $trafficShareGroupId, $totalTXBytes2Charge) {
                    DB::update('UPDATE `users` SET credit = credit - CAST(? AS DECIMAL(13, 4)) WHERE id = ?', [$price2Charge, $userId]);
                    $creditRecord = UserCreditRecord::query()->create([
                        "user_id" => $userId,
                        "amount" => "-" . $price2Charge,
                        "type" => CreditRecordType::TYPE_CHARGE_PUBLIC_NETWORK_TX_TRAFFIC,
                        "relative_object_id" => $trafficShareGroupId,
                        "description" => sprintf("TX traffic usage: %s bytes, month: %s, traffic share group: %s.", $totalTXBytes2Charge, $month, is_null($trafficShareGroupId) ? 'default' : $trafficShareGroupId)
                    ]);
                    TrafficShareGroupChargeHistory::query()->create([
                        "traffic_share_group_id" => $trafficShareGroupId,
                        "type" => CreditRecordType::TYPE_CHARGE_PUBLIC_NETWORK_TX_TRAFFIC,
                        "amount" => $price2Charge,
                        "created_at" => $creditRecord->created_at->__toString(),
                    ]);
                    TrafficUsage::finishCharging($userId, $trafficShareGroupId);
                });

                if (is_callable($afterCharge)) {
                    try {
                        $afterCharge($this, $trafficShareGroupId, $totalUnchargedRXBytes, $totalUnchargedTXBytes, $userFreeTXTrafficAtShareGroup, $totalTXBytes2Charge, $txUnitPrice, 0, $price2Charge);
                    } catch (\Throwable $throwable) {}
                }
            }
        } finally {
            TrafficUsage::clearCharging();
        }
    }

    /**
     * @param $dateFormat
     * @param $minTime
     * @param bool $groupByType
     * @return \Illuminate\Database\Query\Builder
     */
    public function commonCreditRecordBuilder($dateFormat, $minTime, $groupByType = false)
    {
        $groupBy = ["time"];
        $additionalSelect = "";
        if ($groupByType) {
            $groupBy[] = "type";
            $additionalSelect = "type, ";
        }
        return DB::table("user_credit_records")
            ->selectRaw($additionalSelect . "DATE_FORMAT(created_at, '". $dateFormat ."') AS time, SUM(amount) AS total_amount")
            ->where("amount", "<", 0)
            ->where("user_id", $this->id)
            ->where("created_at", ">=", $minTime)
            ->whereIn("type", CreditRecordType::CONSUMPTION_CREDIT_RECORD_TYPE)
            // ->orderByDesc("id")
            ->groupBy(... $groupBy)
            ;
    }

    public function prepareForExclusiveOperation()
    {
        return self::query()
            ->where("id", $this->id)
            ->where(function ($builder) {
                $builder
                    ->orWhereNull("charging_by")
                    ->orWhereRaw("charging_by NOT IN (SELECT id FROM INFORMATION_SCHEMA.PROCESSLIST)")
                ;
            })
            ->update(Common::chargingItemValues());
    }

    public function completeExclusiveOperation()
    {
        return self::query()
            ->where("id", $this->id)
            ->where("charging_by", Common::connectionIDRawQuery())
            ->update([
                "charging_by" => null,
                "charging_started_at" => null,
            ]);
    }

    public static function prepareChargeableItems($max = 1, &$maxLastChargedTime = null)
    {
        if (is_null($maxLastChargedTime))
            $maxLastChargedTime = Common::nowHourly();

        return self::query()
            ->whereHas("trafficUsages", function ($query) {
                $query
                    ->whereNull("last_charged_at")
                    ->where("network_type", NetworkType::TYPE_PUBLIC_NETWORK)
                ;
            })
            ->whereNull("charging_by")
            ->where(function (Builder $builder) use ($maxLastChargedTime) {
                $builder
                    ->orWhere("last_charged_at", "<", $maxLastChargedTime)
                    ->orWhereNull("last_charged_at")
                ;
            })
            ->limit($max)
            ->update(Common::chargingItemValues())
            ;
        /*
        return DB::update(<<<EOF
UPDATE users
SET charging_by         = CONNECTION_ID(),
    charging_started_at = ?
WHERE EXISTS(SELECT user_id FROM compute_instance_traffic_usages WHERE compute_instance_traffic_usages.user_id = users.id AND compute_instance_traffic_usages.last_charged_at IS NULL)
AND charging_by IS NULL
AND (last_charged_at < ? OR last_charged_at IS NULL)
LIMIT ?
EOF
, [date("Y-m-d H:i:s"), $maxLastChargedTime, intval($max)]);
        */
    }

    public static function getChargeableUsers()
    {
        return self::query()->where("charging_by", Common::connectionIDRawQuery())->get();
    }
}