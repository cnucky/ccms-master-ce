<?php

namespace App\Http\Middleware\LocalVolume;

use App\ComputeInstance\LocalVolume;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class OperationRequestLimit extends \App\Http\Middleware\OperationRequestLimit
{
    protected function retrieveModel(Request $request)
    {
        $model = $request->route()->parameter("localVolume");
        if ($model instanceof LocalVolume)
            return $model;
        return LocalVolume::query()->findOrFail($model);
    }

    protected function retrieveNode(Model $model)
    {
        return Common::retrieveComputeNode($model);
    }
}