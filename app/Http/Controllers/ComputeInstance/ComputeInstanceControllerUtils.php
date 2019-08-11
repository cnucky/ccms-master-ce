<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-21
 * Time: 下午6:57
 */

namespace App\Http\Controllers\ComputeInstance;


use App\ComputeInstance;
use App\Constants\ComputeInstanceStatusCode;
use App\Node\ComputeNode;
use Illuminate\Http\Request;

abstract class ComputeInstanceControllerUtils
{
    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function retrieveOperableComputeInstances(Request $request)
    {
        return ComputeInstance::query()->whereIn("id", $request->items)
            ->whereDoesntHave("processingOperationRequests")
            ->where("status", ComputeInstanceStatusCode::STATUS_NORMAL)
            ->whereHas("node", function ($builder) {
                return ComputeNode::whereOnline($builder);
            })
            ;
    }

    public static function whereCurrentUserInstancesClosure(Request $request)
    {
        return function ($builder) use ($request) {
            $builder->where("user_id", $request->user()->id);
        };
    }
}