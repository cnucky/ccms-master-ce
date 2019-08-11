<?php

namespace App\Http\Controllers;

use App\ComputeInstance;
use App\ComputeResourceOperationRequest;
use App\Http\Controllers\ModelControllers\FilterableIndexController;
use Illuminate\Http\Request;

class ComputeResourceOperationRequestIndexController extends FilterableIndexController
{
    public function indexByComputeInstance(Request $request, ComputeInstance $computeInstance)
    {
        return [
            "result" => true,
            "operationRequests" => $this->paginate($request, $computeInstance->operationRequests()->orderByDesc("id"))
        ];
    }
}
