<?php

namespace EuaCreations\LaravelIam\Support;

use EuaCreations\LaravelIam\Models\Role;
use Illuminate\Console\Command;

class RoleCreator
{
    /**
     * Create a role with the given attributes.
     */
    public static function create(
        Command $command,
        string $slug,
        string $name,
        bool $autoAssign,
        bool $builtin
    ): void {
        $role = Role::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'guard_name' => 'web',
                'is_builtin' => $builtin,
                'auto_assign_new_features' => $autoAssign,
            ]
        );

        $command->info("Role '{$role->slug}' is ready.");
    }
}
