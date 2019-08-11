<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-2
 * Time: 下午11:55
 */

namespace App\Utils;


use Illuminate\Support\Facades\Log;

class Common
{
    public static function logException(\Throwable $throwable)
    {
        Log::error($throwable->getMessage(), ["exception" => $throwable]);
    }

    public static function generateTradeNo()
    {
        return env("TRADE_NO_PREFIX", "CCMS") . self::generateTradeNoSuffix();
    }

    public static function generateTradeRefundNo()
    {
        return env("TRADE_NO_PREFIX", "CCMSR") . self::generateTradeNoSuffix();
    }

    public static function generateTradeNoSuffix()
    {
        list($microsecond, $second) = explode(" ", microtime());

        $microsecond2Second = substr($microsecond, 2, 5);

        return date("YmdHis", $second) . $microsecond2Second . mt_rand(1000, 9999);
    }

    public static function date(&$year = null, &$month = null, &$day = null, &$hour = null, &$minute = null, &$second = null)
    {
        $now = date("Y-m-d-H-i-s");
        list($year, $month, $day, $hour, $minute, $second) = explode("-", $now);
    }

    public static function lastMonth($format = "Y-m-d H:i:s")
    {
        self::date($year, $month, $day);
        return date($format, strtotime(sprintf("%s-%s-%s", $year, $month, $day)) - 2592000);
    }

    public static function lastYear()
    {
        self::date($year, $month);
        return sprintf("%s-%s", intval($year) - 1, $month);
    }
}