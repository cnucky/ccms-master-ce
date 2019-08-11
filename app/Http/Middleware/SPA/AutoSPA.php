<?php

namespace App\Http\Middleware\SPA;

use Closure;
use Symfony\Component\HttpFoundation\Response;

abstract class AutoSPA
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
        // Continue on expect JSON or non-get request
        if ($request->expectsJson() || !$request->isMethod("get")) {
            /**
             * @var Response $response
             */
            $response = $next($request);
        } else {
            $response = response($this->getSPAView());
        }

        $response->headers->set("Cache-Control", "no-cache, no-store, must-revalidate");

        return $response;
    }

    abstract protected function getSPAView();
}
