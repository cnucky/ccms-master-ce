<?php
/**
 * Created by PhpStorm.
 * Date: 19-2-28
 * Time: 下午9:07
 */

namespace App\Jobs\ComputeInstance;


use App\Constants\ComputeInstanceStatusCode;

class PowerOn extends OperationRequest
{
    public function operationRequestHandle()
    {
        $this->getComputeInstance()->createUtils()->powerOn();
        $this->getComputeInstance()->update(["power_status" => ComputeInstanceStatusCode::POWER_STATUS_RUNNING]);
    }
}