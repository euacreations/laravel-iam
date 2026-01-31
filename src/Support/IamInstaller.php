<?php

namespace EuaCreations\LaravelIam\Support;

use EuaCreations\LaravelIam\Models\Role;

class IamInstaller
{
    public static function ensureBuiltinRoles(): void
    {
        
        // Make sure table exists
        if (!\Schema::hasTable(config('iam.tables.roles'))) {
            return;
        }
        
        $builtinRoles = config('iam.builtin_roles', []);

        foreach ($builtinRoles as $slug => $options) {
            Role::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => ucfirst($slug),
                    'guard_name' => 'web',
                    'is_builtin' => true,
                    'auto_assign_new_permissions' =>
                        $options['auto_assign_new_permissions'] ?? false,
                ]
            );
        }
    }
}
