<?php

namespace EuaCreations\LaravelIam\Support;

use EuaCreations\LaravelIam\Models\Role;
use Illuminate\Console\Command;

class RoleShower
{
    /**
     * Show role details and its features.
     */
    public static function show(Command $command, string $roleValue, string $roleField): void
    {
        $role = Role::where($roleField, $roleValue)->first();
        if (! $role) {
            $command->error("Role not found by {$roleField}={$roleValue}.");
            return;
        }

        $featureCount = $role->features()->count();

        $command->table(
            ['ID', 'Name', 'Slug', 'Built-in', 'Auto-Assign', 'Feature Count'],
            [[
                $role->id,
                $role->name,
                $role->slug,
                $role->is_builtin ? 'yes' : 'no',
                $role->auto_assign_new_features ? 'yes' : 'no',
                $featureCount,
            ]]
        );

        FeatureLister::list($command, (string) $role->slug, null, false);
    }
}
