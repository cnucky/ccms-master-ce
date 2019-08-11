<?php

namespace App\Http\Controllers\ComputeInstance;

use App\ComputeInstance;
use App\Constants\ComputeInstance\OperationRequest\StatusCode;
use App\Constants\ComputeInstance\OperationRequest\TypeCode;
use App\Constants\ComputeInstanceStatusCode;
use App\Constants\ComputeResourceOperation\ResourceTypeCode;
use App\Jobs\ComputeInstance\PowerOff;
use App\Jobs\ComputeInstance\PowerOn;
use App\Jobs\ComputeInstance\PowerReset;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class PowerController extends Controller
{
    public function on(ComputeInstance $computeInstance)
    {
        PowerOn::dispatch($operationRequest = $this->createOperationRequest($computeInstance, TypeCode::TYPE_POWER_ON));
        return ["result" => true, "operationRequest" => $operationRequest];
    }

    public function massOn(Request $request)
    {
        $computeInstanceModelList = $this->retrieveComputeInstances($request, ComputeInstanceStatusCode::POWER_STATUS_OFF);

        return $this->doMassOn($request, $computeInstanceModelList);
    }

    public function massOnForAdmin(Request $request)
    {
        $computeInstanceModelList = $this->retrieveComputeInstancesForAdmin($request, ComputeInstanceStatusCode::POWER_STATUS_OFF);

        return $this->doMassOn($request, $computeInstanceModelList);
    }

    private function doMassOn(Request $request, $computeInstanceModelList)
    {
        $operationRequests = [];
        foreach ($computeInstanceModelList as $computeInstance) {
            try {
                PowerOn::dispatch($operationRequest = $this->createOperationRequest($computeInstance, TypeCode::TYPE_POWER_ON));
                $operationRequests[] = $operationRequest;
            } catch (QueryException $e) {
            }
        }

        return ["result" => true, "operationRequests" => $operationRequests];
    }

    public function off(ComputeInstance $computeInstance)
    {
        PowerOff::dispatch($operationRequest = $this->createOperationRequest($computeInstance, TypeCode::TYPE_POWER_OFF));
        return ["result" => true, "operationRequest" => $operationRequest];
    }

    public function massOff(Request $request)
    {
        $computeInstanceModelList = $this->retrieveComputeInstances($request, ComputeInstanceStatusCode::POWER_STATUS_RUNNING);

        return $this->doMassOff($request, $computeInstanceModelList);
    }

    public function massOffForAdmin(Request $request)
    {
        $computeInstanceModelList = $this->retrieveComputeInstancesForAdmin($request, ComputeInstanceStatusCode::POWER_STATUS_RUNNING);

        return $this->doMassOff($request, $computeInstanceModelList);
    }

    private function doMassOff(Request $request, $computeInstanceModelList)
    {
        $operationRequests = [];
        foreach ($computeInstanceModelList as $computeInstance) {
            try {
                PowerOff::dispatch($operationRequest = $this->createOperationRequest($computeInstance, TypeCode::TYPE_POWER_OFF));
                $operationRequests[] = $operationRequest;
            } catch (QueryException $e) {
            }
        }

        return ["result" => true, "operationRequests" => $operationRequests];
    }

    public function reset(ComputeInstance $computeInstance)
    {
        PowerReset::dispatch($operationRequest = $this->createOperationRequest($computeInstance, TypeCode::TYPE_POWER_RESET));
        return ["result" => true, "operationRequest" => $operationRequest];
    }

    public function massReset(Request $request)
    {
        $computeInstanceModelList = $this->retrieveComputeInstances($request, ComputeInstanceStatusCode::POWER_STATUS_RUNNING);

        return $this->doMassReset($request, $computeInstanceModelList);
    }

    public function massResetForAdmin(Request $request)
    {
        $computeInstanceModelList = $this->retrieveComputeInstancesForAdmin($request, ComputeInstanceStatusCode::POWER_STATUS_RUNNING);

        return $this->doMassReset($request, $computeInstanceModelList);
    }

    private function doMassReset(Request $request, $computeInstanceModelList)
    {
        $operationRequests = [];
        foreach ($computeInstanceModelList as $computeInstance) {
            try {
                PowerReset::dispatch($operationRequest = $this->createOperationRequest($computeInstance, TypeCode::TYPE_POWER_RESET));
                $operationRequests[] = $operationRequest;
            } catch (QueryException $e) {
            }
        }

        return ["result" => true, "operationRequests" => $operationRequests];
    }

    private function retrieveComputeInstances(Request $request, $witPowerStatus)
    {
        return ComputeInstanceControllerUtils::retrieveOperableComputeInstances($request)
            ->where(ComputeInstanceControllerUtils::whereCurrentUserInstancesClosure($request))
            ->where("power_status", $witPowerStatus)
            ->get()
            ;
    }

    private function retrieveComputeInstancesForAdmin(Request $request, $witPowerStatus)
    {
        return ComputeInstanceControllerUtils::retrieveOperableComputeInstances($request)
            ->where("power_status", $witPowerStatus)
            ->get()
            ;
    }

    /**
     * @param $id
     * @param $type
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    private function createOperationRequest(ComputeInstance $computeInstance, $type)
    {
        return ComputeInstance\OperationRequest::query()->create([
            "user_id" => $computeInstance->user_id,
            "resource_type" => ResourceTypeCode::TYPE_COMPUTE_INSTANCE,
            "resource_id" => $computeInstance->id,
            "type" => $type,
            "operation_status" => StatusCode::STATUS_WAIT_FOR_SUBMITTING_TO_NODE,
        ]);
    }
}
