<?php

namespace EuaCreations\LaravelIam\Console;

use EuaCreations\LaravelIam\Support\FeaturePruner;
use Illuminate\Console\Command;

class FeaturePruneCommand extends Command
{
    protected $signature = 'iam:feature-prune {--dry-run : Show orphans without deleting}';
    protected $description = 'Remove orphan features not found in routes';

    /**
     * Execute the command.
     */
    public function handle(): void
    {
        FeaturePruner::prune($this, (bool) $this->option('dry-run'));
    }
}
