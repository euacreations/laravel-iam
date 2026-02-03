<?php

namespace EuaCreations\LaravelIam\Support;

use EuaCreations\LaravelIam\Models\Feature;
use EuaCreations\LaravelIam\Models\Role;
use Illuminate\Console\Command;

class FeatureLister
{
    /**
     * List features in a table view.
     */
    public static function list(
        Command $command,
        ?string $roleValue = null,
        ?string $group = null,
        bool $builtinOnly = false
    ): void {
        $query = Feature::query();

        if ($roleValue) {
            $role = Role::where('slug', $roleValue)
                ->orWhere('id', $roleValue)
                ->first();

            if (! $role) {
                $command->error("Role not found for '{$roleValue}'.");
                return;
            }

            $query = $role->features();
        }

        if ($group) {
            $query->where('group', $group);
        }

        if ($builtinOnly) {
            $query->where('is_builtin', true);
        }

        $features = $query->orderBy('id')->get(['id', 'name', 'slug', 'group', 'is_builtin']);

        if ($features->isEmpty()) {
            $command->info('No features found.');
            return;
        }

        $command->table(
            ['ID', 'Name', 'Slug', 'Group', 'Built-in'],
            $features->map(fn ($feature) => [
                $feature->id,
                $feature->name,
                $feature->slug,
                $feature->group ?? '-',
                $feature->is_builtin ? 'yes' : 'no',
            ])->all()
        );
    }
}
