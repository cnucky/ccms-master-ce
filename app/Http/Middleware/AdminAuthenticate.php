<?php

namespace App\Http\Middleware;

use App\Constants\StatusCode;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class AdminAuthenticate
{
    const GUARD_NAME = "admin";

    /**
     * The authentication factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next)
    {
        $this->auth->shouldUse(self::GUARD_NAME);

        if (!$request->user(self::GUARD_NAME)) {
            $this->throwAuthenticationException();
        }
        if ($request->user(self::GUARD_NAME)->status !== StatusCode::STATUS_NORMAL) {
            Auth::guard(self::GUARD_NAME)->logout();
            $request->session()->invalidate();
            $this->throwAuthenticationException();
        }
        return $next($request);
    }

    private function throwAuthenticationException()
    {
        throw new AuthenticationException("Unauthenticated.", ["admin"]);
    }
}
