<?php

namespace EuaCreations\LaravelIam\Support;

use EuaCreations\LaravelIam\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class IamInstaller
{
    /**
     * Run the IAM installation flow.
     */
    public static function install(Command $command): void
    {
        $command->info('Installing IAM package...');

        // Schema check
        if (! Schema::hasTable(config('iam.tables.roles'))) {
            $command->error('IAM tables not found. Did you run migrations?');
            return;
        }

        /** @var class-string<\Illuminate\Database\Eloquent\Model> $userModel */
        $userModel = config('iam.user_model', \App\Models\User::class);
        $firstUser = $userModel::orderBy('id')->first();

        // CASE 1: No users
        if (! $firstUser) {
            self::ensureBuiltinRoles(skipAdmin: true);

            $command->warn('No users found.');
            $command->line('Please register the first admin user.');
            $command->line('Then rerun: php artisan iam:install');

            return;
        }

        // CASE 2: Users exist
        self::ensureBuiltinRoles();

        self::assignAdminRoleToFirstUser($firstUser, $command);

        $command->info('IAM installation completed successfully.');
    }

    /**
     * Ensure built-in roles exist in storage.
     */
    public static function ensureBuiltinRoles(bool $skipAdmin = false): void
    {
        $builtinRoles = config('iam.builtin_roles', []);

        foreach ($builtinRoles as $slug => $options) {
            if ($skipAdmin && $slug === 'admin') {
                continue;
            }

            Role::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => ucfirst($slug),
                    'guard_name' => 'web',
                    'is_builtin' => true,
                    'auto_assign_new_features' =>
                        $options['auto_assign_new_features'] ?? false,
                ]
            );
        }
    }

    protected static function assignAdminRoleToFirstUser(
        Model $user,
        Command $command
    ): void {
        $adminRole = Role::where('slug', 'admin')->first();

        if (! $adminRole) {
            $command->error('Admin role not found.');
            return;
        }

        if ($user->role_id === $adminRole->id) {
            $command->line('First user is already an admin.');
            return;
        }

        $user->update([
            'role_id' => $adminRole->id,
        ]);
        $command->info("Admin role assigned to {$user->username}");
    }
}
