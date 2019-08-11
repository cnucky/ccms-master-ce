<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-21
 * Time: 下午8:26
 */

return [
    \App\Constants\ComputeInstanceStatusCode::STATUS_NORMAL => "正常",
    \App\Constants\ComputeInstanceStatusCode::STATUS_CREATING => "创建中",
    \App\Constants\ComputeInstanceStatusCode::STATUS_CONFIGURING => "配置中",
    \App\Constants\ComputeInstanceStatusCode::STATUS_LACK_OF_CREDIT => "欠费",
    \App\Constants\ComputeInstanceStatusCode::STATUS_SUSPENDED => "已锁定",
    \App\Constants\ComputeInstanceStatusCode::STATUS_DESTROYING => "销毁中",
    \App\Constants\ComputeInstanceStatusCode::STATUS_CREATE_UNSUCCESSFULLY => "创建失败",
    \App\Constants\ComputeInstanceStatusCode::STATUS_DESTROY_UNSUCCESSFULLY => "销毁失败",
];