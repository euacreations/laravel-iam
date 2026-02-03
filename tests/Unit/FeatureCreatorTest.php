<?php

use EuaCreations\LaravelIam\Models\Role;
use EuaCreations\LaravelIam\Support\FeatureCreator;
use Tests\Fixtures\FakeCommand;

it('creates a feature and auto-assigns to eligible roles', function () {
    $role = Role::create([
        'name' => 'Admin',
        'slug' => 'admin',
        'guard_name' => 'web',
        'is_builtin' => true,
        'auto_assign_new_features' => true,
    ]);

    $command = new FakeCommand();
    FeatureCreator::create($command, 'profile.edit', 'Profile Edit', null);

    expect($role->features()->where('slug', 'profile.edit')->exists())->toBeTrue();
});
