<?php

namespace EuaCreations\LaravelIam;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class LaravelIamServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/iam.php' => config_path('iam.php'),
        ], 'iam-config');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->app['router']->aliasMiddleware(
            'feature',
            \EuaCreations\LaravelIam\Middleware\FeatureMiddleware::class
        );
        $this->app['router']->aliasMiddleware(
            'skip-feature',
            \EuaCreations\LaravelIam\Middleware\SkipFeatureMiddleware::class
        );

        if (config('iam.global_enforce')) {
            $this->app['router']->pushMiddlewareToGroup(
                'web',
                \EuaCreations\LaravelIam\Middleware\GlobalFeatureMiddleware::class
            );
        }

        Gate::before(function ($user, $ability) {
            if (method_exists($user, 'hasFeature')) {
                return $user->hasFeature($ability) ? true : null;
            }

            return null;
        });
    }

    /**
     * Register package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/iam.php', 'iam');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \EuaCreations\LaravelIam\Console\UserListCommand::class,
                \EuaCreations\LaravelIam\Console\IamInstallCommand::class,
                \EuaCreations\LaravelIam\Console\FeatureCreateCommand::class,
                \EuaCreations\LaravelIam\Console\FeatureSyncCommand::class,
                \EuaCreations\LaravelIam\Console\FeaturePruneCommand::class,
                \EuaCreations\LaravelIam\Console\RoleCreateCommand::class,
                \EuaCreations\LaravelIam\Console\RoleAssignCommand::class,
                \EuaCreations\LaravelIam\Console\RoleFeatureCommand::class,
                \EuaCreations\LaravelIam\Console\RoleListCommand::class,
                \EuaCreations\LaravelIam\Console\RoleShowCommand::class,
                \EuaCreations\LaravelIam\Console\UserListCommand::class,
                \EuaCreations\LaravelIam\Console\FeatureListCommand::class,
            ]);
        }
    }
}
