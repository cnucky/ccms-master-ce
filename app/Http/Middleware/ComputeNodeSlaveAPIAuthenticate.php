<?php

namespace App\Http\Middleware;

use App\Node\ComputeNode;
use Closure;

class ComputeNodeSlaveAPIAuthenticate
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
        $computeNodeInstance = $request->route()->parameter("computeNode");
        if (!($computeNodeInstance instanceof ComputeNode)) {
            $computeNodeInstance = ComputeNode::query()->findOrFail($computeNodeInstance);
        }

        $token = $request->header("CCMS-Token");

        if (!password_verify($token, $computeNodeInstance->token))
            return response(["result" => false, "message" => "Invalid token"]);

        return $next($request);
    }
}
