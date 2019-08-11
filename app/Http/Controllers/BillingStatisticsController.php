<?php

namespace App\Http\Controllers;

use App\ComputeInstance;
use App\ComputeInstancePackage;
use App\Constants\AdminPermissions;
use App\Constants\CreditRecordType;
use App\IPPool\IPv4Assignment;
use App\IPPool\IPv6Assignment;
use App\Module\Constants\Payment\TradeRefundStatus;
use App\Module\Constants\Payment\TradeStatus;
use App\PaymentTrade;
use App\PaymentTradeRefund;
use App\User;
use App\UserCreditRecord;
use App\Utils\Common;
use App\Utils\Time;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use YunInternet\CCMSCommon\Constants\NetworkType;

class BillingStatisticsController extends Controller
{
    public function dashboard()
    {
        $today = date("Y-m-d 00:00:00");
        return [
            "result" => true,
            "todayTotalAddCredit" => PaymentTrade::query()->where("created_at", ">=", $today)
                ->where("status", TradeStatus::STATUS_PAID)
                ->sum("fee_in_default_currency"),
            "totalTotalRefund" => PaymentTradeRefund::query()
                ->where("created_at", ">=", $today)
                ->where("status", TradeRefundStatus::STATUS_SUCCEED)
                ->sum("fee_in_default_currency"),
            "unpaid" => str_replace("-", "", User::query()->where("credit", "<", "0")->sum("credit")),
            "instanceCount" => ComputeInstance::query()->count(),
            "instanceHourlyIncoming" => ComputeInstance::calculateTotalHourlyPrice(),
            "instanceTodayTotalCharged" => str_replace("-", "", UserCreditRecord::query()->where("type", CreditRecordType::TYPE_CHARGE_COMPUTE_INSTANCE)->where("created_at", ">=", $today)->sum("amount")),
            "localVolumeCount" => ComputeInstance\LocalVolume::query()->count(),
            "localVolumeCapacityCount" => ComputeInstance\LocalVolume::query()->sum("capacity"),
            "localVolumeHourlyIncoming" => ComputeInstance\LocalVolume::calculateTotalHourlyPrice(),
            "localVolumeTodayTotalCharged" => $this->sumCreditRecord(CreditRecordType::TYPE_CHARGE_LOCAL_VOLUME, $today),
            "elasticIPv4Count" => IPv4Assignment::query()
                ->whereNotNull("user_id")
                ->where("unbindable", "!=", 0)
                ->count(),
            "elasticIPv4HourlyIncoming" => IPv4Assignment::calculateTotalHourlyPrice(),
            "elasticIPv4TodayTotalCharged" => $this->sumCreditRecord(CreditRecordType::TYPE_CHARGE_ELASTIC_IP_V4, $today),
            "elasticIPv6Count" => IPv6Assignment::query()
                ->whereNotNull("user_id")
                ->where("unbindable", "!=", 0)
                ->count(),
            "elasticIPv6HourlyIncoming" => IPv6Assignment::calculateTotalHourlyPrice(),
            "elasticIPv6TodayTotalCharged" => $this->sumCreditRecord(CreditRecordType::TYPE_CHARGE_ELASTIC_IP_V4, $today),
            "trafficTodayTotalUsed" => DB::table("compute_instance_traffic_usages")
                ->select(DB::raw("IFNULL(SUM(rx_byte_count), 0) AS total_rx_bytes, IFNULL(SUM(tx_byte_count), 0) AS total_tx_bytes"))
                ->where("network_type", NetworkType::TYPE_PUBLIC_NETWORK)
                ->where("microtime", ">=", strtotime($today))
                ->first(),
            "trafficTodayTotalCharged" => $this->sumCreditRecord(CreditRecordType::TYPE_CHARGE_PUBLIC_NETWORK_TX_TRAFFIC, $today),
        ];
    }

    public function dailyTrade(Request $request)
    {
        self::retrieveDayRange($request, $minTime, $maxTime);

        return [
            "result" => true,
            "totalPaid" => PaymentTrade::statistics("%Y-%m-%d", $minTime, $maxTime)->pluck("total_fee", "time"),
            "totalRefunded" => PaymentTradeRefund::statistics("%Y-%m-%d", $minTime, $maxTime)->pluck("total_fee", "time"),
        ];
    }

    public function monthlyTrade(Request $request)
    {
        self::retrieveMonthRange($request, $minTime, $maxTime);

        return [
            "result" => true,
            "totalPaid" => PaymentTrade::statistics("%Y-%m", $minTime, $maxTime)->pluck("total_fee", "time"),
            "totalRefunded" => PaymentTradeRefund::statistics("%Y-%m", $minTime, $maxTime)->pluck("total_fee", "time"),
        ];
    }

    public function dailyCharge(Request $request)
    {
        self::retrieveDayRange($request, $minTime, $maxTime);

        $timeFormat = "%Y-%m-%d";

        return $this->generateChargeStatisticsData($timeFormat, $minTime, $maxTime);
    }

    public function monthlyCharge(Request $request)
    {
        self::retrieveMonthRange($request, $minTime, $maxTime);

        $timeFormat = "%Y-%m";

        return $this->generateChargeStatisticsData($timeFormat, $minTime, $maxTime);
    }

    private function generateChargeStatisticsData($timeFormat, $minTime, $maxTime)
    {
        return [
            "result" => true,
            "instanceTotalCharged" => self::getSumCreditRecordByTimeFormatMap($timeFormat, CreditRecordType::TYPE_CHARGE_COMPUTE_INSTANCE, $minTime, $maxTime),
            "localVolumeTotalCharged" => self::getSumCreditRecordByTimeFormatMap($timeFormat, CreditRecordType::TYPE_CHARGE_LOCAL_VOLUME, $minTime, $maxTime),
            "elasticIPv4TotalCharged" => self::getSumCreditRecordByTimeFormatMap($timeFormat, CreditRecordType::TYPE_CHARGE_ELASTIC_IP_V4, $minTime, $maxTime),
            "elasticIPv6TotalCharged" => self::getSumCreditRecordByTimeFormatMap($timeFormat, CreditRecordType::TYPE_CHARGE_ELASTIC_IP_V6, $minTime, $maxTime),
            "txTrafficTotalCharged" => self::getSumCreditRecordByTimeFormatMap($timeFormat, CreditRecordType::TYPE_CHARGE_PUBLIC_NETWORK_TX_TRAFFIC, $minTime, $maxTime),
        ];
    }

    private function sumCreditRecord($type, $minTime)
    {
        return str_replace("-", "",
            UserCreditRecord::query()
                ->where("type", $type)
                ->where("created_at", ">=", $minTime)
                ->sum("amount")
        );
    }

    private static function sumCreditRecordByTimeFormat($dateFormat, $type, $minTime, $maxTime)
    {
        return DB::table("user_credit_records")
            ->select(DB::raw("DATE_FORMAT(created_at, '". $dateFormat ."') AS time, ABS(IFNULL(SUM(amount), 0)) AS total_amount"))
            ->where("created_at", ">=", $minTime)
            ->where("created_at", "<", $maxTime)
            ->where("type", $type)
            ->groupBy("time")
            ;
    }

    private static function getSumCreditRecordByTimeFormatMap($dateFormat, $type, $minTime, $maxTime)
    {
        return DB::table("user_credit_records")
            ->select(DB::raw("DATE_FORMAT(created_at, '". $dateFormat ."') AS time, ABS(IFNULL(SUM(amount), 0)) AS total_amount"))
            ->where("created_at", ">=", $minTime)
            ->where("created_at", "<", $maxTime)
            ->where("type", $type)
            ->groupBy("time")
            ->pluck("total_amount", "time")
            ;
    }

    private static function retrieveDayRange(Request $request, &$start, &$end)
    {
        Time::retrieveDayRange($request, $start, $end);
    }

    private static function retrieveMonthRange(Request $request, &$start, &$end)
    {
        Time::retrieveMonthRange($request, $start, $end);
    }
}
