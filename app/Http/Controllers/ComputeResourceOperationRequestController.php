<?php

namespace App\Http\Controllers;

use App\ComputeResourceOperationRequest;
use App\Constants\ComputeResourceOperation\ResourceTypeCode;
use Illuminate\Http\Request;

class ComputeResourceOperationRequestController extends Controller
{
    public function query(Request $request)
    {
        return $this->retrieveOperationRequests($request, function () use ($request) {
            return ComputeResourceOperationRequest::query()->where("user_id", $request->user()->id);
        });
    }

    public function queryForAdmin(Request $request)
    {
        return $this->retrieveOperationRequests($request, function () use ($request) {
            return ComputeResourceOperationRequest::query();
        });
    }

    public function computeInstanceQuery(Request $request)
    {
        return $this->retrieveForComputeInstance($request, function () use ($request) {
            return ComputeResourceOperationRequest::query()->where("user_id", $request->user()->id);
        });

    }

    public function computeInstanceQueryForAdmin(Request $request)
    {
        return $this->retrieveForComputeInstance($request, function () use ($request) {
            return ComputeResourceOperationRequest::query();
        });
    }


    private function retrieveOperationRequests(Request $request, callable $queryBuilderGetter)
    {
        $operationRequestList = $request->operationRequestList;


        if ($request->needResource) {
            $operationRequests = [];
            foreach (ResourceTypeCode::AVAILABLE_TYPE_LIST as $resourceTypeCode) {
                if (array_key_exists($resourceTypeCode, ResourceTypeCode::TYPE_MAP_2_RELATION_NAME)) {
                    $operationRequestWithType = call_user_func($queryBuilderGetter)->where("resource_type", $resourceTypeCode)->whereIn("id", $operationRequestList)->with(ResourceTypeCode::TYPE_MAP_2_RELATION_NAME[$resourceTypeCode])->get()->toArray();
                    $operationRequests = array_merge($operationRequests, $operationRequestWithType);
                }
            }
        } else {
            $operationRequests = call_user_func($queryBuilderGetter)->whereIn("id", $operationRequestList)->get();
        }

        return ["result" => true, "operationRequests" => $operationRequests];

    }

    private function retrieveForComputeInstance(Request $request, callable $queryBuilderGetter)
    {
        $operationRequestList = $request->operationRequestList;

        $operationRequests = call_user_func($queryBuilderGetter)
            ->where("resource_type", ResourceTypeCode::TYPE_COMPUTE_INSTANCE)
            ->whereIn("id", $operationRequestList)
            ->with("instance:id,unique_id,name")
            ->get()
        ;

        return ["result" => true, "operationRequests" => $operationRequests];
    }

    /*
    private function getQueryBuilder(Request $request)
    {
        return ComputeResourceOperationRequest::query()->where("user_id", $request->user()->id);
    }
    */
}
