<?php

namespace EuaCreations\LaravelIam\Support;

use EuaCreations\LaravelIam\Models\Feature;
use EuaCreations\LaravelIam\Models\Role;
use Illuminate\Console\Command;

class RoleFeatureManager
{
    /**
     * Add or remove features for a role.
     *
     * @param  string[]  $featureValues
     */
    public static function update(
        Command $command,
        string $roleValue,
        array $featureValues,
        string $roleField,
        string $featureField,
        bool $remove
    ): void {
        $role = Role::where($roleField, $roleValue)->first();
        if (! $role) {
            $command->error("Role not found by {$roleField}={$roleValue}.");
            return;
        }

        $features = Feature::whereIn($featureField, $featureValues)->get();
        if ($features->isEmpty()) {
            $command->error('No matching features found.');
            return;
        }

        $ids = $features->pluck('id')->all();
        if ($remove) {
            $role->features()->detach($ids);
            $command->info('Features removed from role.');
            return;
        }

        $role->features()->syncWithoutDetaching($ids);
        $command->info('Features added to role.');
    }
}
