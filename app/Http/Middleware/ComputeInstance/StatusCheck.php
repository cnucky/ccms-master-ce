<?php

namespace App\Http\Middleware\ComputeInstance;

use App\Constants\ComputeInstanceStatusCode;
use App\Constants\GlobalErrorCode;
use App\Exceptions\CCMSAPIException;
use Closure;

class StatusCheck
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
        $computeInstance = Common::retrieveComputeInstanceModel($request);
        if ($computeInstance->status === ComputeInstanceStatusCode::STATUS_SUSPENDED)
            throw new CCMSAPIException("SUSPENDED", GlobalErrorCode::LOCKED);
        else if ($computeInstance->status !== ComputeInstanceStatusCode::STATUS_NORMAL)
            throw new CCMSAPIException("INVALID_STATUS", GlobalErrorCode::COMPUTE_INSTANCE_INVALID_STATUS);
        return $next($request);
    }
}
