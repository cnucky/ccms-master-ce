<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-14
 * Time: 下午8:42
 */

namespace App\Constants;


interface ComputeInstanceStatusCode
{
    const STATUS_NORMAL = 0; // 正常
    const STATUS_CREATING = 1; // 创建中
    const STATUS_CONFIGURING = 2; // 配置中
    const STATUS_LACK_OF_CREDIT = 3; // 欠费停机
    const STATUS_SUSPENDED = 5; // 已锁定
    const STATUS_DESTROYING = 6; // 销毁中
    const STATUS_CREATE_UNSUCCESSFULLY = 8;
    const STATUS_DESTROY_UNSUCCESSFULLY = 9;

    const POWER_STATUS_RUNNING = 1; // 运行中
    const POWER_STATUS_OFF = 0; // 已关机

    const AVAILABLE_STATUS = [
        self::STATUS_NORMAL,
        self::STATUS_CREATING,
        self::STATUS_CONFIGURING,
        self::STATUS_LACK_OF_CREDIT,
        self::STATUS_SUSPENDED,
        self::STATUS_DESTROYING,
    ];

    const AVAILABLE_POWER_STATUS = [
        self::POWER_STATUS_RUNNING,
        self::POWER_STATUS_OFF,
    ];
}