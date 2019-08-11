<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-18
 * Time: 下午8:12
 */

namespace App\Constants\ComputeResourceOperation;


interface Handler
{
    const HANDLERS = [
        ResourceTypeCode::TYPE_COMPUTE_INSTANCE => \App\Constants\ComputeInstance\OperationRequest\Handler::HANDLERS,
        ResourceTypeCode::TYPE_LOCAL_VOLUME => \App\Constants\Volume\OperationRequest\Handler::HANDLERS,
    ];
}