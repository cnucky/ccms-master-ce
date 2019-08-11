<?php

namespace App\Http\Middleware\LocalVolume;

use App\Constants\GlobalErrorCode;
use App\Exceptions\CCMSAPIException;
use App\Utils\Volume\Constants;
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
        $localVolume = Common::retrieveModel($request);
        if ($localVolume->status === Constants::STATUS_LOCK || $localVolume->status !== Constants::STATUS_NORMAL)
            throw new CCMSAPIException("INVALID_STATUS", GlobalErrorCode::LOCAL_VOLUME_INVALID_STATUS);
        return $next($request);
    }
}
