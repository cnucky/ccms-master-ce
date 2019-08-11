<?php

namespace App\Http\Middleware;

use App\ComputeInstance;
use App\Constants\ComputeResourceOperation\StatusCode;
use App\Constants\GlobalErrorCode;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class OperationRequestLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $model = $this->getModel($request);
        if (strtotime($this->retrieveNode($model)->last_communicated_at) <= time() - 600) {
            return response(["result" => false, "errno" => GlobalErrorCode::NODE_OFFLINE]);
        }
        $processingOperationRequests = $this->getProcessingOperationRequest($model);
        if (count($processingOperationRequests))
            return response(["result" => false, "errno" => GlobalErrorCode::OPERATION_REQUEST_LIMIT, "operationRequests" => $processingOperationRequests]);
        return $next($request);
    }

    protected function getModel(Request $request)
    {
        return $this->retrieveModel($request);
    }

    protected function getProcessingOperationRequest(Model $model)
    {
        $operationRequests = $model->operationRequests()
            ->where("is_processed", 0)
            /*
            ->orWhere(function ($builder) use ($request) {
                $builder
                    ->where("resource_type", "!=", -1)
                    ->whereNull("resource_id")
                    ->whereNotIn("operation_status", [
                        StatusCode::STATUS_SUCCESS,
                        StatusCode::STATUS_FAILED,
                    ])
                    ->where("user_id", $request->user()->id);
            })
            */
            ->get()
        ;

        /*
        $massOperationRequests = ComputeResourceOperationRequest::query()
            ->where("resource_type", "!=", -1)
            ->whereNull("resource_id")
            ->whereNotIn("operation_status", [
                StatusCode::STATUS_SUCCESS,
                StatusCode::STATUS_FAILED,
            ])
            ->where("user_id", $request->user()->id)
            ->get()
            ->toArray()
        ;

        return array_merge($operationRequests, $massOperationRequests);
        */
        return $operationRequests;
    }

    /**
     * @param Request $request
     * @return ComputeInstance|ComputeInstance\LocalVolume
     */
    abstract protected function retrieveModel(Request $request);

    abstract protected function retrieveNode(Model $model);
}