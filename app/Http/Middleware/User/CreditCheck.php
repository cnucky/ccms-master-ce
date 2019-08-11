<?php

namespace App\Http\Middleware\User;

use App\Constants\GlobalErrorCode;
use App\Exceptions\CCMSAPIException;
use Closure;

class CreditCheck
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
        if ($request->user()->credit < 0)
            throw new CCMSAPIException("CREDIT", GlobalErrorCode::CREDIT);
        return $next($request);
    }
}
