<?php

namespace EuaCreations\LaravelIam\Support;

use EuaCreations\LaravelIam\Models\Role;
use Illuminate\Console\Command;

class UserLister
{
    /**
     * List users with optional role filtering.
     */
    public static function list(Command $command, ?string $roleValue, string $userField): void
    {
        $userModel = config('iam.user_model', \App\Models\User::class);

        $query = $userModel::query();

        if ($roleValue) {
            $role = Role::where('slug', $roleValue)
                ->orWhere('id', $roleValue)
                ->first();

            if (! $role) {
                $command->error("Role not found for '{$roleValue}'.");
                return;
            }

            $query->where('role_id', $role->id);
        }

        $users = $query->orderBy('id')->get(['id', $userField, 'role_id']);

        if ($users->isEmpty()) {
            $command->info('No users found.');
            return;
        }

        $command->table(
            ['ID', ucfirst($userField), 'Role ID'],
            $users->map(fn ($user) => [
                $user->id,
                $user->{$userField},
                $user->role_id ?? '-',
            ])->all()
        );
    }
}
