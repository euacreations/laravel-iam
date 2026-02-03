<?php

namespace EuaCreations\LaravelIam\Support;

use EuaCreations\LaravelIam\Models\Role;
use Illuminate\Console\Command;

class RoleLister
{
    /**
     * List roles in a table view.
     */
    public static function list(Command $command, bool $builtinOnly = false): void
    {
        $query = Role::query();
        if ($builtinOnly) {
            $query->where('is_builtin', true);
        }

        $roles = $query->orderBy('id')->get(['id', 'name', 'slug', 'is_builtin', 'auto_assign_new_features']);

        if ($roles->isEmpty()) {
            $command->info('No roles found.');
            return;
        }

        $command->table(
            ['ID', 'Name', 'Slug', 'Built-in', 'Auto-Assign'],
            $roles->map(fn ($role) => [
                $role->id,
                $role->name,
                $role->slug,
                $role->is_builtin ? 'yes' : 'no',
                $role->auto_assign_new_features ? 'yes' : 'no',
            ])->all()
        );
    }
}
