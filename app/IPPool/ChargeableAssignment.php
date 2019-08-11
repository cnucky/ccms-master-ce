<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-2
 * Time: 下午7:35
 */

namespace App\IPPool;


use App\ChargeHistory\IPv4PoolChargeHistory;
use App\ChargeHistory\IPv6PoolChargeHistory;
use App\Constants\CreditRecordType;
use App\User;
use App\Utils\Charging\ChargeableModel;

trait ChargeableAssignment
{
    use ChargeableModel;

    public function getUser(): User
    {
        return $this->user;
    }

    public function getHourlyUnitPrice(): string
    {
        return $this->pool->price_per_hour;
    }

    protected static function whereNeedChargeableItem($builder)
    {
        $builder
            ->whereNotNull("user_id")
            ->where("unbindable", "=", 1)
        ;
    }

    public function getLastChargedTime(): string
    {
        $lastChargedTime = $this->last_charged_at;
        if (is_null($lastChargedTime))
            return $this->assigned_at;
        return $lastChargedTime;
    }

    public function getCreditRecordDescription($lastChargeTime, $currentChargingTime, $chargeTimeInSecond)
    {
        return sprintf("%s/%s: %s - %s (%s seconds)", $this->human_readable_first_usable, $this->pool->subnet_network_bits, $lastChargeTime, $currentChargingTime, $chargeTimeInSecond);
    }

    protected function createRelativeChargeHistory($amount, $time)
    {
        $values = [
            "pool_id" => $this->pool_id,
            "amount" => $amount,
            "created_at" => $time,
        ];

        if ($this->getCreditRecordType() === CreditRecordType::TYPE_CHARGE_ELASTIC_IP_V4) {
            IPv4PoolChargeHistory::query()->create($values);
        } else {
            IPv6PoolChargeHistory::query()->create($values);
        }
    }
}