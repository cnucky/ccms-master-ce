<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-2
 * Time: 下午3:13
 */

namespace App\Utils\Charging;

use App\Exceptions\IsBeingCharged;
use App\User;
use App\UserCreditRecord;
use App\Utils\Charging\Exception\ChargingException;
use App\Utils\Charging\Exception\InvalidLastChargedTimeException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

trait ChargeableModel
{
    use LastChargedTimeGetter;

    use FixNonExistsOwner;

    use FinishCharging;

    use ClearCharging;

    use TimeRangeAsCreditRecordDescription;

    abstract public function getUser(): User;

    abstract public function getHourlyUnitPrice(): string;

    abstract public function getCreditRecordType() : int;

    abstract protected function createRelativeChargeHistory($amount, $time);

    public static function beginCharging($beforeCharge = null, $afterCharged = null, $onError = null)
    {
        Common::databaseSupportCheck();

        if (!is_callable($beforeCharge))
            $beforeCharge = function ($chargeableItem, $user, $hourlyPrice, $amount, $chargeTimeInSecond, $price2Charge) {
            };
        if (!is_callable($afterCharged))
            $afterCharged = function ($chargeableItem, $user, $hourlyPrice, $amount, $chargeTimeInSecond, $price2Charge) {
            };
        if (is_null($onError))
            $onError = function (\Throwable $throwable) {};

        self::clearCharging();
        try {
            while (self::prepareChargeableItems(16, $currentChargingTime)) {
                /**
                 * @var static $chargeableItem
                 */
                foreach (self::getChargeableItems() as $chargeableItem) {
                    try {
                        $chargeableItem->charge($currentChargingTime, $beforeCharge, $afterCharged);
                    } catch (\Throwable $throwable) {
                        \App\Utils\Common::logException($throwable);

                        try {
                            $onError($throwable);
                        } catch (\Throwable $throwable) {
                            \App\Utils\Common::logException($throwable);
                        }

                        continue;
                    }
                }
            }
        } finally {
            self::clearCharging();
        }
    }

    /**
     * @param int $max
     * @param null|string $maxLastChargedTime
     * @return int
     */
    public static function prepareChargeableItems($max = 16, &$maxLastChargedTime = null)
    {
        if (is_null($maxLastChargedTime))
            $maxLastChargedTime = Common::nowHourly();

        return self::query()
            ->limit($max)
            ->where(function ($builder) {
                self::whereNeedChargeableItem($builder);
            })
            ->whereNull("charging_by")
            ->where(function ($builder) use ($maxLastChargedTime) {
                $builder
                    ->orWhere("last_charged_at", "<", $maxLastChargedTime)
                    ->orWhereNull("last_charged_at");
            })
            ->update(Common::chargingItemValues());
    }

    protected static function whereNeedChargeableItem($builder)
    {
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getChargeableItems()
    {
        return self::query()->where("charging_by", Common::connectionIDRawQuery())->get();
    }

    /**
     * @param string $currentChargingTime
     * @param null|callable $beforeCharge
     * @param null|callable $afterCharged
     * @throws ChargingException
     */
    public function charge($currentChargingTime, $beforeCharge = null, $afterCharged = null)
    {
        // $this->chargeRelativeResources($beforeCharge, $afterCharged);

        $chargeTimeInSecond = $this->lastChargedTimeDiff2($currentChargingTime, $lastChargeTime);
        $hourlyPrice = $this->getHourlyUnitPrice();
        $amount = $this->getAmount();
        $user = $this->getUser();

        $price2ChargeResult = DB::select("SELECT CAST(CAST(? AS DECIMAL(13, 4)) * (CAST(? AS DECIMAL(13, 4)) / 3600) * CAST(? AS DECIMAL(13, 4)) AS DECIMAL(13, 4)) AS price_2_charge", [$hourlyPrice, $chargeTimeInSecond, $amount]);
        $price2Charge = $price2ChargeResult[0]->price_2_charge;

        try {
            if (is_callable($beforeCharge)) {
                $beforeCharge($this, $user, $hourlyPrice, $amount, $chargeTimeInSecond, $price2Charge);
            }
        } catch (\Throwable $throwable) {
            \App\Utils\Common::logException($throwable);
        }

        DB::transaction(function () use ($lastChargeTime, $currentChargingTime, $user, $hourlyPrice, $chargeTimeInSecond, $price2Charge) {
            DB::update('UPDATE `users` SET credit = credit - CAST(? AS DECIMAL(13, 4)) WHERE id = ?', [$price2Charge, $user->id]);
            $creditRecord = UserCreditRecord::query()->create([
                "user_id" => $user->id,
                "amount" => "-" . $price2Charge,
                "type" => $this->getCreditRecordType(),
                "description" => $this->getCreditRecordDescription($lastChargeTime, $currentChargingTime, $chargeTimeInSecond),
                "relative_object_id" => $this->id,
            ]);
            $this->createRelativeChargeHistory($price2Charge, $creditRecord->created_at->__toString());
            $this->finishCharging($currentChargingTime);
        });

        try {
            if (is_callable($afterCharged)) {
                $user->refresh();
                $afterCharged($this, $user, $hourlyPrice, $amount, $chargeTimeInSecond, $price2Charge);
            }
        } catch (\Throwable $throwable) {
            \App\Utils\Common::logException($throwable);
        }
        DB::rollback();
    }

    public function chargeRelativeResources()
    {
    }

    public function getAmount()
    {
        return 1;
    }

    /**
     * @param $time
     * @param $lastChargeTime
     * @return int
     * @throws ChargingException
     */
    public function lastChargedTimeDiff2($time, &$lastChargeTime): int
    {
        $unixTimestampTime = $time;
        if (is_string($time))
            $unixTimestampTime = strtotime($time);
        if ($unixTimestampTime === false)
            throw new ChargingException("Invalid current charge time");

        $lastChargedAtAsUnixTimestamp = strtotime($lastChargeTime = $this->getLastChargedTime());

        if ($lastChargedAtAsUnixTimestamp === false)
            throw new InvalidLastChargedTimeException("Invalid last charged time");

        $timeDiffInSecond = $unixTimestampTime - $lastChargedAtAsUnixTimestamp;
        if ($timeDiffInSecond < 0)
            throw new ChargingException("Current charging time $unixTimestampTime less than last charged time $lastChargedAtAsUnixTimestamp");
        return $timeDiffInSecond;
    }

    public function chargingPrepare(): int
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

    public static function massPrepare()
    {

    }

    public static function massCharge($models)
    {
        if ($models instanceof Model)
            $models = [$models];
        $now = date("Y-m-d H:i:s");
        /**
         * @var static $model
         */
        foreach ($models as $model) {
            if (!$model->chargingPrepare()) {
                throw new IsBeingCharged();
            }
            $model->charge($now);
        }
        return true;
    }
}