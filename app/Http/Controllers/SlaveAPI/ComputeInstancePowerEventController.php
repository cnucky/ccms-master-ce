<?php

namespace App\Http\Controllers\SlaveAPI;

use App\ComputeInstance;
use App\Constants\ComputeInstanceStatusCode;
use App\Node\ComputeNode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ComputeInstancePowerEventController extends Controller
{
    public function started($computeNode, ComputeInstance $computeInstance)
    {
        $this->updatePowerStatus($computeInstance, ComputeInstanceStatusCode::POWER_STATUS_RUNNING);
        return $this->makeSuccessResponse();
    }

    public function stopped($computeNode, ComputeInstance $computeInstance)
    {
        $this->updatePowerStatus($computeInstance, ComputeInstanceStatusCode::POWER_STATUS_OFF);
        return $this->makeSuccessResponse();
    }

    private function updatePowerStatus(ComputeInstance $computeInstance, $powerStatus)
    {
        $computeInstance->update(["power_status" => $powerStatus]);
    }

    private function makeSuccessResponse()
    {
        return ["result" => true];
    }
}
