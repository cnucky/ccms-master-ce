<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-28
 * Time: 下午8:08
 */

namespace App\Constants\ComputeInstance\OperationRequest;


use App\Jobs\ComputeInstance\ChangeHostname;
use App\Jobs\ComputeInstance\ChangeMedia;
use App\Jobs\ComputeInstance\ChangeNetworkInterfaceModel;
use App\Jobs\ComputeInstance\ChangePublicImage;
use App\Jobs\ComputeInstance\Destroy;
use App\Jobs\ComputeInstance\PowerOff;
use App\Jobs\ComputeInstance\PowerOn;
use App\Jobs\ComputeInstance\PowerReset;
use App\Jobs\ComputeInstance\Reconfigure;
use App\Jobs\ComputeInstance\ChangeOSPassword;
use App\Jobs\ComputeInstance\ReconfigureOSNetwork;
use App\Jobs\ComputeInstance\Setup;
use App\Jobs\ComputeInstance\UpdateIPAddress;

interface Handler
{
    const HANDLERS = [
        TypeCode::TYPE_SETUP => Setup::class,
        TypeCode::TYPE_POWER_ON => PowerOn::class,
        TypeCode::TYPE_POWER_OFF => PowerOff::class,
        TypeCode::TYPE_POWER_RESET => PowerReset::class,
        TypeCode::TYPE_DESTROY => Destroy::class,
        TypeCode::TYPE_CHANGE_MEDIA => ChangeMedia::class,
        TypeCode::TYPE_UPDATE_IP_ADDRESS => UpdateIPAddress::class,
        TypeCode::TYPE_RECONFIGURE => Reconfigure::class,
        TypeCode::TYPE_CHANGE_NETWORK_INTERFACE_MODEL => ChangeNetworkInterfaceModel::class,
        TypeCode::TYPE_CHANGE_HOSTNAME => ChangeHostname::class,
        TypeCode::TYPE_CHANGE_OS_PASSWORD => ChangeOSPassword::class,
        TypeCode::TYPE_CHANGE_PUBLIC_IMAGE => ChangePublicImage::class,
        TypeCode::TYPE_RECONFIGURE_OS_NETWORK => ReconfigureOSNetwork::class,
    ];
}