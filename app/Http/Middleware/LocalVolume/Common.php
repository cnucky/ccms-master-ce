<?php
/**
 * Created by PhpStorm.
 * Date: 19-5-6
 * Time: 下午9:58
 */

namespace App\Http\Middleware\LocalVolume;


use App\ComputeInstance\LocalVolume;
use App\Node\ComputeNode;
use Illuminate\Http\Request;

abstract class Common
{
    public static function retrieveModel(Request $request) : LocalVolume
    {
        $model = $request->route()->parameter("localVolume");
        if ($model instanceof LocalVolume)
            return $model;
        return LocalVolume::query()->findOrFail($model);
    }

    public static function retrieveComputeNode(LocalVolume $localVolume) : ComputeNode
    {
        return $localVolume->node;
    }
}