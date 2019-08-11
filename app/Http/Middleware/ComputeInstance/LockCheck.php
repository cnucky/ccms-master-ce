<?php

namespace App\Http\Middleware\ComputeInstance;

use App\Constants\ComputeInstanceStatusCode;
use App\Constants\GlobalErrorCode;
use App\Exceptions\CCMSAPIException;
use Closure;

class LockCheck
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
        return $next($request);
    }
}
