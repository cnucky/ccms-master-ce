<?php

namespace App\Http\Middleware\ComputeInstance;

use App\ComputeInstance;
use App\Constants\ComputeInstance\OperationRequest\StatusCode;
use App\Constants\GlobalErrorCode;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class OperationRequestLimit extends \App\Http\Middleware\OperationRequestLimit
{
    protected function retrieveModel(Request $request)
    {
        $computeInstance = $request->route()->parameter("computeInstance");
        if ($computeInstance instanceof ComputeInstance)
            return $computeInstance;
        return ComputeInstance::query()->findOrFail($computeInstance);
    }

    protected function retrieveNode(Model $model)
    {
        return Common::retrieveComputeNode($model);
    }
}