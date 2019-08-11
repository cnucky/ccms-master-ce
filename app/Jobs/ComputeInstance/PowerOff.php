<?php

namespace App\Jobs\ComputeInstance;

use App\Constants\ComputeInstanceStatusCode;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PowerOff extends OperationRequest
{
    protected function operationRequestHandle()
    {
        $this->getComputeInstance()->createUtils()->powerOff();
        $this->getComputeInstance()->update(["power_status" => ComputeInstanceStatusCode::POWER_STATUS_OFF]);
    }
}
