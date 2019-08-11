<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComputeResourceOperationRequestLog extends Model
{
    protected $table = "compute_resource_operation_request_logs";

    protected $guarded = [];

    public function operationRequest()
    {
        return $this->belongsTo(ComputeResourceOperationRequest::class, "operation_request_id");
    }
}
