<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-23
 * Time: 下午1:27
 */

return [
    \App\Constants\ComputeInstance\OperationRequest\TypeCode::TYPE_SETUP => "创建",
    \App\Constants\ComputeInstance\OperationRequest\TypeCode::TYPE_POWER_ON => "启动",
    \App\Constants\ComputeInstance\OperationRequest\TypeCode::TYPE_POWER_OFF => "关机",
    \App\Constants\ComputeInstance\OperationRequest\TypeCode::TYPE_POWER_RESTART => "重启",
    \App\Constants\ComputeInstance\OperationRequest\TypeCode::TYPE_POWER_RESET => "强制重启",
    \App\Constants\ComputeInstance\OperationRequest\TypeCode::TYPE_DESTROY => "销毁",
    \App\Constants\ComputeInstance\OperationRequest\TypeCode::TYPE_CHANGE_MEDIA => "更改虚拟介质",
    \App\Constants\ComputeInstance\OperationRequest\TypeCode::TYPE_UPDATE_IP_ADDRESS => "更新IP地址",
    \App\Constants\ComputeInstance\OperationRequest\TypeCode::TYPE_RECONFIGURE => "重配置",
    \App\Constants\ComputeInstance\OperationRequest\TypeCode::TYPE_CHANGE_NETWORK_INTERFACE_MODEL => "更改虚拟网卡型号",
    \App\Constants\ComputeInstance\OperationRequest\TypeCode::TYPE_CHANGE_HOSTNAME => "更改主机名",
    \App\Constants\ComputeInstance\OperationRequest\TypeCode::TYPE_CHANGE_OS_PASSWORD => "重置系统密码",
    \App\Constants\ComputeInstance\OperationRequest\TypeCode::TYPE_CHANGE_PUBLIC_IMAGE => "更换公共镜像",
    \App\Constants\ComputeInstance\OperationRequest\TypeCode::TYPE_RECONFIGURE_OS_NETWORK => "重置系统网络配置",
];