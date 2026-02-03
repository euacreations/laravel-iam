<?php

namespace EuaCreations\LaravelIam\Console;

use EuaCreations\LaravelIam\Support\RoleFeatureManager;
use Illuminate\Console\Command;

class RoleFeatureCommand extends Command
{
    protected $signature = 'iam:role-feature
                            {role : Role identifier (slug or id)}
                            {features : Comma-separated feature identifiers}
                            {--remove : Remove features instead of adding}
                            {--role-field=slug : Field to match role (slug|id|name)}
                            {--feature-field=slug : Field to match feature (slug|id|name)}';

    protected $description = 'Add or remove features for a role';

    /**
     * Execute the command.
     */
    public function handle(): void
    {
        $features = array_filter(array_map(
            'trim',
            explode(',', (string) $this->argument('features'))
        ));

        if ($features === []) {
            $this->error('No features provided.');
            return;
        }

        RoleFeatureManager::update(
            $this,
            $this->argument('role'),
            $features,
            $this->option('role-field'),
            $this->option('feature-field'),
            (bool) $this->option('remove')
        );
    }
}
