<?php

namespace App\Jobs\ComputeInstance;

use App\Constants\ComputeInstanceStatusCode;

class PowerReset extends OperationRequest
{
    protected function operationRequestHandle()
    {
        $this->getComputeInstance()->createUtils()->powerReset();
        $this->getComputeInstance()->update(["power_status" => ComputeInstanceStatusCode::POWER_STATUS_RUNNING]);
    }
}
