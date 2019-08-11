<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-28
 * Time: 下午7:55
 */

namespace App\Constants\ComputeInstance\OperationRequest;


interface TypeCode
{
    const TYPE_SETUP = 0; // Create compute instance
    const TYPE_POWER_ON = 1;
    const TYPE_POWER_OFF = 2;
    const TYPE_POWER_RESTART = 3;
    const TYPE_POWER_RESET = 5;
    const TYPE_DESTROY = 6;
    const TYPE_CHANGE_MEDIA = 8;
    const TYPE_UPDATE_IP_ADDRESS = 9;
    const TYPE_RECONFIGURE = 10;
    const TYPE_CHANGE_NETWORK_INTERFACE_MODEL = 11;
    const TYPE_CHANGE_HOSTNAME = 12;
    const TYPE_CHANGE_OS_PASSWORD = 13;
    const TYPE_CHANGE_PUBLIC_IMAGE = 15;
    const TYPE_RECONFIGURE_OS_NETWORK = 16;
}