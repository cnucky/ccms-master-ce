<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-18
 * Time: 下午7:31
 */

namespace App\Constants\ComputeResourceOperation;


use App\ComputeInstance;

interface ResourceTypeCode
{
    const TYPE_COMPUTE_INSTANCE = 0;
    const TYPE_LOCAL_VOLUME = 1;
    const TYPE_MASS_COMPUTE_INSTANCE_OPERATION = 2;
    const TYPE_MASS_LOCAL_VOLUME_OPERATION = 3;

    const AVAILABLE_TYPE_LIST = [
        self::TYPE_COMPUTE_INSTANCE,
        self::TYPE_LOCAL_VOLUME,
        self::TYPE_MASS_COMPUTE_INSTANCE_OPERATION,
        self::TYPE_MASS_LOCAL_VOLUME_OPERATION,
    ];

    const TYPE_MAP_2_MODEL_CLASS = [
        self::TYPE_COMPUTE_INSTANCE => ComputeInstance::class,
        self::TYPE_LOCAL_VOLUME => ComputeInstance\LocalVolume::class,
    ];

    const TYPE_MAP_2_RELATION_NAME = [
        self::TYPE_COMPUTE_INSTANCE => "instance",
        self::TYPE_LOCAL_VOLUME => "localVolume",
    ];
}