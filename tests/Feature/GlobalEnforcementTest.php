<?php

use EuaCreations\LaravelIam\Models\Feature;
use EuaCreations\LaravelIam\Models\Role;
use EuaCreations\LaravelIam\Middleware\GlobalFeatureMiddleware;
use Illuminate\Support\Facades\Route;
use Tests\Fixtures\User;

it('enforces global feature access for named routes', function () {
    $role = Role::create([
        'name' => 'Member',
        'slug' => 'member',
        'guard_name' => 'web',
        'is_builtin' => false,
        'auto_assign_new_features' => false,
    ]);

    $user = User::create([
        'username' => 'jane.doe',
        'role_id' => $role->id,
    ]);

    Route::middleware([GlobalFeatureMiddleware::class, 'web', 'auth'])
        ->get('/secure', fn () => 'ok')
        ->name('secure.area');

    $this->actingAs($user)->get('/secure')->assertForbidden();

    $feature = Feature::create([
        'name' => 'Secure Area',
        'slug' => 'secure.area',
        'guard_name' => 'web',
        'is_builtin' => false,
    ]);
    $role->features()->attach($feature->id);

    $user->refresh()->load('role.features');

    $this->actingAs($user)->get('/secure')->assertOk();
});
