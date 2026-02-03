<?php

namespace EuaCreations\LaravelIam\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;

class GlobalFeatureMiddleware
{
    /**
     * Enforce feature access globally for named routes.
     */
    public function handle($request, Closure $next)
    {
        $route = $request->route();
        if (! $route) {
            return $next($request);
        }

        $middleware = $route->gatherMiddleware();
        foreach ($middleware as $item) {
            if (! is_string($item)) {
                continue;
            }

            if ($item === 'skip-feature' || str_starts_with($item, 'feature')) {
                return $next($request);
            }
        }

        $feature = $route->getName();
        if (! $feature) {
            return $next($request);
        }

        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        if (method_exists($user, 'hasFeature')) {
            if ($user->hasFeature($feature)) {
                return $next($request);
            }
        } elseif (Gate::allows($feature)) {
            return $next($request);
        }

        abort(403);
    }
}
