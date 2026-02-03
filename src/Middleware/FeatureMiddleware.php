<?php

namespace EuaCreations\LaravelIam\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;

class FeatureMiddleware
{
    /**
     * Handle a feature-protected request.
     *
     * @param  string|null  $feature
     */
    public function handle($request, Closure $next, ?string $feature = null)
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        if (! $feature) {
            $feature = $request->route()?->getName();
        }

        if (! $feature) {
            abort(403);
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
