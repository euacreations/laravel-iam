<?php

namespace EuaCreations\LaravelIam\Support;

use EuaCreations\LaravelIam\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class UserLister
{
    /**
     * List users with optional role filtering.
     */
    public static function list(Command $command, ?string $roleValue, string $userField): void
    {
        $userModel = config('iam.user_model', \App\Models\User::class);
        $userInstance = new $userModel();
        $userTable = $userInstance->getTable();

        $roleIdExists = Schema::hasColumn($userTable, 'role_id');
        if (! $roleIdExists) {
            $command->warn("The users table is missing the 'role_id' column. Run IAM migrations to add it.");
        }

        if (! Schema::hasColumn($userTable, $userField)) {
            if ($userField === 'username' && Schema::hasColumn($userTable, 'email')) {
                $command->warn("The '{$userField}' column was not found. Falling back to 'email'.");
                $userField = 'email';
            } else {
                $command->warn("The '{$userField}' column was not found in '{$userTable}'. Please specify a valid column.");
                $command->line('Usage: php artisan iam:user-list --user-field=email');
                $command->line('Example: php artisan iam:user-list --user-field=name');
                return;
            }
        }

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

        $columns = ['id', $userField];
        if ($roleIdExists) {
            $columns[] = 'role_id';
        }

        $users = $query->orderBy('id')->get($columns);

        if ($users->isEmpty()) {
            $command->info('No users found.');
            return;
        }

        $headers = ['ID', ucfirst($userField)];
        if ($roleIdExists) {
            $headers[] = 'Role ID';
        }

        $rows = $users->map(function ($user) use ($userField, $roleIdExists) {
            $row = [
                $user->id,
                $user->{$userField},
            ];

            if ($roleIdExists) {
                $row[] = $user->role_id ?? '-';
            }

            return $row;
        })->all();

        $command->table($headers, $rows);
    }
}
