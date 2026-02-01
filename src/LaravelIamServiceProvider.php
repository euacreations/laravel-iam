<?php

namespace EuaCreations\LaravelIam;

use Illuminate\Support\ServiceProvider;
//use EuaCreations\LaravelIam\Support\IamInstaller;

class LaravelIamServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([__DIR__.'/../config/iam.php' => config_path('iam.php'),], 'iam-config');
        $this->mergeConfigFrom(__DIR__.'/../config/iam.php','iam');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \EuaCreations\LaravelIam\Console\IamInstallCommand::class,
                \EuaCreations\LaravelIam\Console\PermissionCreateCommand::class,
            ]);
        }
    }
}


