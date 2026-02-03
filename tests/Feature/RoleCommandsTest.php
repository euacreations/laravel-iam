<?php

use EuaCreations\LaravelIam\Models\Feature;
use EuaCreations\LaravelIam\Models\Role;
use Illuminate\Support\Facades\Artisan;
use Tests\Fixtures\User;

it('assigns a role to a user', function () {
    $role = Role::create([
        'name' => 'Admin',
        'slug' => 'admin',
        'guard_name' => 'web',
        'is_builtin' => true,
        'auto_assign_new_features' => false,
    ]);

    $user = User::create([
        'username' => 'john.doe',
    ]);

    Artisan::call('iam:role-assign', [
        'user' => $user->id,
        'role' => $role->slug,
    ]);

    expect($user->fresh()->role_id)->toBe($role->id);
});

it('adds and removes features for a role', function () {
    $role = Role::create([
        'name' => 'Editor',
        'slug' => 'editor',
        'guard_name' => 'web',
        'is_builtin' => false,
        'auto_assign_new_features' => false,
    ]);

    $feature = Feature::create([
        'name' => 'Edit Profile',
        'slug' => 'profile.edit',
        'guard_name' => 'web',
        'is_builtin' => false,
    ]);

    Artisan::call('iam:role-feature', [
        'role' => $role->slug,
        'features' => $feature->slug,
    ]);
    expect($role->features()->whereKey($feature->id)->exists())->toBeTrue();

    Artisan::call('iam:role-feature', [
        'role' => $role->slug,
        'features' => $feature->slug,
        '--remove' => true,
    ]);
    expect($role->features()->whereKey($feature->id)->exists())->toBeFalse();
});
