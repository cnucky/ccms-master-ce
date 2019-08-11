<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-23
 * Time: 下午2:46
 */

return [
    \App\Constants\ComputeResourceOperation\StatusCode::STATUS_WAIT_FOR_SUBMITTING_TO_NODE => "待处理",
    \App\Constants\ComputeResourceOperation\StatusCode::STATUS_WAIT_FOR_NODE_RESPONSE => "待回应",
    \App\Constants\ComputeResourceOperation\StatusCode::STATUS_SUCCESS => "成功",
    \App\Constants\ComputeResourceOperation\StatusCode::STATUS_FAILED => "失败",
    \App\Constants\ComputeResourceOperation\StatusCode::STATUS_WAIT_FOR_DISPATCHING => "入队中",
    \App\Constants\ComputeResourceOperation\StatusCode::STATUS_PROCESSING => "处理中",
    \App\Constants\ComputeResourceOperation\StatusCode::STATUS_WAIT_FOR_RETRY => "等待重试",
];