<?php

namespace App\ComputeInstance;

use App\ComputeInstance;
use App\ComputeResourceOperationRequest;
use App\Constants\ComputeInstance\OperationRequest\Handler;
use App\Constants\ComputeInstance\OperationRequest\StatusCode;
use App\Constants\ComputeResourceOperation\ResourceTypeCode;
use Illuminate\Support\Facades\DB;

class OperationRequest extends ComputeResourceOperationRequest
{
    public function dispatch()
    {
        $handler = Handler::HANDLERS[$this->type];
        ([$handler, "dispatch"])($this);
    }

    /**
     * @param $computeInstance
     * @param $type
     * @param null $data
     * @param int $initialStatus
     * @return self
     */
    public static function newRequest($computeInstance, $type, $data = null, $initialStatus = StatusCode::STATUS_WAIT_FOR_DISPATCHING)
    {
        if (!($computeInstance instanceof ComputeInstance))
            $computeInstance = ComputeInstance::query()->findOrFail($computeInstance);
        return self::newOperationRequest($computeInstance->user_id, ResourceTypeCode::TYPE_COMPUTE_INSTANCE, $computeInstance->id, $type, $data, $initialStatus);
    }

    public static function newRequestThenDispatch($computeInstance, $type, $data = null, $initialStatus = StatusCode::STATUS_PROCESSING)
    {
        DB::transaction(function () use ($computeInstance, $type, $data, $initialStatus, &$operationRequest) {
            $operationRequest = self::newRequest($computeInstance, $type, $data, $initialStatus);
            $operationRequest->dispatch();
        });

        return $operationRequest;
    }
}
