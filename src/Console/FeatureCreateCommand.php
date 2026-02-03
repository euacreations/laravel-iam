<?php

namespace EuaCreations\LaravelIam\Console;

use EuaCreations\LaravelIam\Support\FeatureCreator;
use Illuminate\Console\Command;

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

        FeatureCreator::create($this, $slug, $name, $group);
    }
}
