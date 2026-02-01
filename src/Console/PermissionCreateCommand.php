<?php

namespace EuaCreations\LaravelIam\Console;

use Illuminate\Console\Command;
use EuaCreations\LaravelIam\Models\Permission;
use EuaCreations\LaravelIam\Models\Role;

class PermissionCreateCommand extends Command
{
    /**
     * Command signature
     *
     * Usage:
     * php artisan iam:permission-create user.create "Create User" --group=user
     */
    protected $signature = 'iam:permission-create
                            {slug : Unique machine name of the permission, e.g. user.create}
                            {name : Human-readable name of the permission, e.g. Create User}
                            {--group= : Optional group/category for the permission}';

    /**
     * Command description
     */
    protected $description = 'Create a new permission in IAM and auto-assign to roles flagged for auto-assignment';

    /**
     * Handle the command
     */
    public function handle(): void
    {
        $slug = $this->argument('slug');
        $name = $this->argument('name');
        $group = $this->option('group') ?? null;

        // Check if permission already exists
        $permission = Permission::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'group' => $group,
                'guard_name' => 'web', // default guard
                'is_builtin' => false,  // user-created
            ]
        );

        $this->info("Permission '{$permission->slug}' has been created successfully.");

        // Auto-assign to roles flagged for auto-assignment
        $roles = Role::where('auto_assign_new_permissions', true)->get();
        foreach ($roles as $role) {
            $role->permissions()->syncWithoutDetaching($permission->id);
            $this->info("Permission '{$permission->slug}' assigned to role '{$role->slug}'.");
        }
    }
}
