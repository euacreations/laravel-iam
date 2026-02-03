<?php

namespace EuaCreations\LaravelIam\Support;

use EuaCreations\LaravelIam\Models\Feature;
use EuaCreations\LaravelIam\Models\Role;
use Illuminate\Console\Command;

class FeatureCreator
{
    /**
     * Create a feature and auto-assign it to eligible roles.
     */
    public static function create(Command $command, string $slug, string $name, ?string $group = null): void
    {
        $feature = Feature::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'group' => $group,
                'guard_name' => 'web',
                'is_builtin' => false,
            ]
        );

        $command->info("Feature '{$feature->slug}' has been created successfully.");

        $roles = Role::where('auto_assign_new_features', true)->get();
        foreach ($roles as $role) {
            $role->features()->syncWithoutDetaching($feature->id);
            $command->info("Feature '{$feature->slug}' assigned to role '{$role->slug}'.");
        }
    }
}
