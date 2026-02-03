<?php

namespace EuaCreations\LaravelIam\Support;

use EuaCreations\LaravelIam\Models\Role;
use Illuminate\Console\Command;

class RoleAssigner
{
    /**
     * Assign a role to a user.
     */
    public static function assign(
        Command $command,
        string $userValue,
        string $roleValue,
        string $userField,
        string $roleField
    ): void {
        $userModel = config('iam.user_model', \App\Models\User::class);

        /** @var \Illuminate\Database\Eloquent\Model|null $user */
        $user = $userModel::where($userField, $userValue)->first();
        if (! $user) {
            $command->error("User not found by {$userField}={$userValue}.");
            return;
        }

        $role = Role::where($roleField, $roleValue)->first();
        if (! $role) {
            $command->error("Role not found by {$roleField}={$roleValue}.");
            return;
        }

        $user->update(['role_id' => $role->id]);
        $command->info("Assigned role '{$role->slug}' to user {$userField}={$userValue}.");
    }
}
