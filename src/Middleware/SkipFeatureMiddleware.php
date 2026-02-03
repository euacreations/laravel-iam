<?php

namespace EuaCreations\LaravelIam\Middleware;

use Closure;

class SkipFeatureMiddleware
{
    /**
     * Skip global feature enforcement for this request.
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
