<?php

namespace Tests;

use EuaCreations\LaravelIam\LaravelIamServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [LaravelIamServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('app.cipher', 'AES-256-CBC');

        $app['config']->set('iam.user_model', \Tests\Fixtures\User::class);
        $app['config']->set('auth.providers.users.model', \Tests\Fixtures\User::class);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app['router']->middlewareGroup('web', []);

        $this->loadMigrationsFrom(__DIR__.'/Database/migrations');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->artisan('migrate');
    }
}
