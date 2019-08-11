<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-2
 * Time: 下午3:17
 */

namespace App\Utils\Charging;


use App\Utils\Charging\Exception\DatabaseNotSupportPrecisionMathException;
use Illuminate\Support\Facades\DB;

abstract class Common
{
    public static function databaseSupportCheck()
    {
        $testResult = DB::select("SELECT (.1 + .2) = .3 AS test_result");
        if ($testResult[0]->test_result !== 1)
            throw new DatabaseNotSupportPrecisionMathException();
        $testResult = DB::select("SELECT (CAST('0.1' AS DECIMAL(13, 4)) + CAST('0.2' AS DECIMAL(13, 4))) = .3 AS test_result");
        if ($testResult[0]->test_result !== 1)
            throw new DatabaseNotSupportPrecisionMathException();
    }

    public static function now()
    {
        return date("Y-m-d H:i:s");
    }

    public static function nowHourly()
    {
        return date("Y-m-d H:00:00");
    }

    public static function connectionIDRawQuery()
    {
        return DB::raw("CONNECTION_ID()");
    }

    public static function chargingItemValues()
    {
        return [
            "charging_by" => self::connectionIDRawQuery(),
            "charging_started_at" => self::now(),
        ];
    }
}