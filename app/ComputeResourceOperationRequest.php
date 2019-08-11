<?php

namespace App;

use App\ComputeInstance\LocalVolume;
use App\Constants\ComputeInstance\OperationRequest\StatusCode;
use App\Constants\ComputeResourceOperation\Handler;
use App\Constants\ComputeResourceOperation\ResourceTypeCode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class ComputeResourceOperationRequest extends Model
{
    protected $table = "compute_resource_operation_requests";

    protected $guarded = [];

    protected $hidden = [
        "sensitive_data",
    ];

    /*
    public function lockedResources()
    {
        return $this->hasMany(ComputeResourceLocks::class, "operation_id");
    }
    */

    /**
     * Do not use ->with("resource")
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     * @throws \Exception
     */
    public function resource()
    {
        $resourceType = $this->resource_type;
        $className = @ResourceTypeCode::TYPE_MAP_2_MODEL_CLASS[$resourceType];
        if (empty($className))
            throw new \Exception("Invalid resource type");
        return $this->belongsTo($className, "resource_id");
    }

    public function instance()
    {
        return $this->belongs2Class(ComputeInstance::class);
    }

    public function localVolume()
    {
        return $this->belongs2Class(LocalVolume::class);
    }

    public function dispatchOperationRequest()
    {
        $handler = Handler::HANDLERS[$this->resource_type][$this->type];
        ([$handler, "dispatch"])($this);
    }

    /**
     * @param int $userId
     * @param int $resourceType
     * @param int $resourceId
     * @param int $operationType
     * @param mixed $data
     * @param int $initialStatus
     * @return \Illuminate\Database\Eloquent\Builder|Model
     */
    public static function newOperationRequest($userId, $resourceType, $resourceId, $operationType, $data = null, $initialStatus = StatusCode::STATUS_WAIT_FOR_DISPATCHING)
    {
        if (is_array($data) || is_object($data))
            $data = json_encode($data);

        return static::query()->create([
            "user_id" => $userId,
            "resource_type" => $resourceType,
            "resource_id" => $resourceId,
            "type" => $operationType,
            "data" => $data,
            "operation_status" => $initialStatus,
        ]);
    }

    public static function newOperationRequestThenDispatch($userId, $resourceType, $resourceId, $operationType, $data = null, $initialStatus = StatusCode::STATUS_PROCESSING)
    {
        DB::transaction(function () use ($userId, $resourceType, $resourceId, $operationType, $data, $initialStatus, &$operationRequest) {
            $operationRequest = self::newOperationRequest($userId, $resourceType, $resourceId, $operationType, $data, $initialStatus);
            $operationRequest->dispatchOperationRequest();
        });

        return $operationRequest;
    }

    protected function belongs2Class($className)
    {
        return $this->belongsTo($className, "resource_id");
    }
}
