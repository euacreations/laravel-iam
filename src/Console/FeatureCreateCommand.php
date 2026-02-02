<?php

namespace EuaCreations\LaravelIam\Console;

use Illuminate\Console\Command;
use EuaCreations\LaravelIam\Models\Feature;
use EuaCreations\LaravelIam\Models\Role;

class FeatureCreateCommand extends Command
{
    /**
     * Command signature
     *
     * Usage:
     * php artisan iam:feature-create user.create "Create User" --group=user
     */
    protected $signature = 'iam:feature-create
                            {slug : Unique machine name of the feature, e.g. user.create}
                            {name : Human-readable name of the feature, e.g. Create User}
                            {--group= : Optional group/category for the feature}';

    /**
     * Command description
     */
    protected $description = 'Create a new feature in IAM and auto-assign to roles flagged for auto-assignment';

    /**
     * Handle the command
     */
    public function handle(): void
    {
        $slug = $this->argument('slug');
        $name = $this->argument('name');
        $group = $this->option('group') ?? null;

        // Check if feature already exists
        $feature = Feature::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'group' => $group,
                'guard_name' => 'web', // default guard
                'is_builtin' => false,  // user-created
            ]
        );

        $this->info("Feature '{$feature->slug}' has been created successfully.");

        // Auto-assign to roles flagged for auto-assignment
        $roles = Role::where('auto_assign_new_features', true)->get();
        foreach ($roles as $role) {
            $role->features()->syncWithoutDetaching($feature->id);
            $this->info("Feature '{$feature->slug}' assigned to role '{$role->slug}'.");
        }
    }
}
