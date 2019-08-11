<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-30
 * Time: 下午4:27
 */

namespace App\Utils;

use Illuminate\Http\Request;

class Time
{
    public static function getServerTimezone()
    {
        return date_default_timezone_get();
    }

    public static function getServerTime()
    {
        return date("Y-m-d H:i:s");
    }

    public static function getServerUnixTimestamp()
    {
        return time();
    }

    public static function retrieveDayRange(Request $request, &$start, &$end)
    {
        $minTime = false;
        if ($startAsTimeStamp = self::retrieveTimeAsUnixTimestamp($request->start)) {
            $minTime = date("Y-m-d 00:00:00", $startAsTimeStamp);
        }
        if ($minTime === false) {
            $minTime = date("Y-m-d 00:00:00", strtotime("previous month"));
        }

        $maxTime = false;
        if ($startAsTimeStamp = self::retrieveTimeAsUnixTimestamp($request->end)) {
            $maxTime = date("Y-m-d 00:00:00", $startAsTimeStamp + 86400);
        }
        if ($maxTime === false) {
            $maxTime = date("Y-m-d 00:00:00", strtotime("next day"));
        }

        $start = $minTime;
        $end = $maxTime;
    }

    public static function retrieveMonthRange(Request $request, &$start, &$end)
    {
        $minTime = false;
        if ($startAsTimeStamp = self::retrieveTimeAsUnixTimestamp($request->start)) {
            $minTime = date("Y-m-01 00:00:00", $startAsTimeStamp);
        }
        if ($minTime === false) {
            $minTime = date("Y-m-01 00:00:00", strtotime("previous year"));
        }

        $maxTime = false;
        if ($startAsTimeStamp = self::retrieveTimeAsUnixTimestamp($request->end)) {
            $maxTime = date("Y-m-01 00:00:00", strtotime("+1 month", $startAsTimeStamp));
        }
        if ($maxTime === false) {
            $maxTime = date("Y-m-01 00:00:00", strtotime("next month"));
        }

        $start = $minTime;
        $end = $maxTime;
    }

    public static function retrieveTimeAsUnixTimestamp($value)
    {
        if ($value) {
            $unixTimestamp = strtotime($value);
            if ($unixTimestamp) {
                return $unixTimestamp;
            }
        }
        return false;
    }
}