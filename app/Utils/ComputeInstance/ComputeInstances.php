<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-18
 * Time: 下午5:22
 */

namespace App\Utils\ComputeInstance;


use App\Constants\AvailableSystemConfigurations;
use App\SystemConfiguration;
use YunInternet\CCMSCommon\Utils\UUID;

abstract class ComputeInstances
{
    const UNIQUE_ID_PREFIX = "ci-";

    public static function generateUniqueId($namespace)
    {
        if (is_numeric($namespace))
            $namespace = self::encodeInteger($namespace);
        else {
            $namespace = bin2hex($namespace);
        }

        $uniqueId =
            $namespace .
            self::encodeInteger(time()) .
            self::encodeInteger(random_int(1000000, 9999999))
        ;

        return $uniqueId;
    }

    public static function generatePassword()
    {
        return base64_encode(random_bytes(11));
    }

    public static function generateVNCPassword($entropy = null)
    {
        return substr(base64_encode(openssl_random_pseudo_bytes(8)), 0, 8);
    }

    protected static function encodeInteger($integer)
    {
        return base_convert($integer, 10, 36);
    }
}