<?php
/**
 * Created by PhpStorm.
 * Date: 19-3-19
 * Time: 上午2:28
 */

namespace App\Utils\ComputeInstance;


use App\ComputeInstance;
use App\Constants\GlobalErrorCode;

abstract class OperationRequestLimitChecker
{
    /**
     * @param ComputeInstance $computeInstance
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public static function check(ComputeInstance $computeInstance)
    {
        $processingOperationRequests = $computeInstance->processingOperationRequests;
        if (count($processingOperationRequests))
            return response(["result" => false, "errno" => GlobalErrorCode::OPERATION_REQUEST_LIMIT, "operationRequests" => $processingOperationRequests]);
        return true;
    }
}