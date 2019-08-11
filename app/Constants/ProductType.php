<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-10
 * Time: 下午6:36
 */

namespace App\Constants;


use App\ComputeInstance;
use App\IPPool\IPv4Assignment;
use App\IPPool\IPv6Assignment;

interface ProductType
{
    const TYPE_COMPUTE_INSTANCE = 0;
    const TYPE_LOCAL_VOLUME = 1;
    const TYPE_IP_V4 = 2;
    const TYPE_IP_V6 = 3;

    const MODEL_MAP = [
        self::TYPE_COMPUTE_INSTANCE => ComputeInstance::class,
        self::TYPE_LOCAL_VOLUME => ComputeInstance\LocalVolume::class,
        self::TYPE_IP_V4 => IPv4Assignment::class,
        self::TYPE_IP_V6 => IPv6Assignment::class,
    ];
}