<?php

namespace EuaCreations\LaravelIam\Console;

use EuaCreations\LaravelIam\Support\FeatureLister;
use Illuminate\Console\Command;

class FeatureListCommand extends Command
{
    protected $signature = 'iam:feature-list
                            {--role= : Filter by role (slug or id)}
                            {--group= : Filter by group}
                            {--builtin : Only show built-in features}';

    protected $description = 'List IAM features';

    /**
     * Execute the command.
     */
    public function handle(): void
    {
        FeatureLister::list(
            $this,
            $this->option('role') ?: null,
            $this->option('group') ?: null,
            (bool) $this->option('builtin')
        );
    }
}
