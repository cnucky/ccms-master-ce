<?php
/**
 * Created by PhpStorm.
 * Date: 19-4-15
 * Time: 下午9:02
 */

namespace App\Http\Middleware\ComputeInstance;


use App\ComputeInstance;
use App\Node\ComputeNode;
use Illuminate\Http\Request;

abstract class Common
{
    public static function retrieveComputeInstanceModel(Request $request) : ComputeInstance
    {
        $computeInstance = $request->route()->parameter("computeInstance");
        if ($computeInstance instanceof ComputeInstance)
            return $computeInstance;
        return ComputeInstance::query()->findOrFail($computeInstance);
    }

    public static function retrieveComputeNode(ComputeInstance $computeInstance) : ComputeNode
    {
        return $computeInstance->node;
    }
}