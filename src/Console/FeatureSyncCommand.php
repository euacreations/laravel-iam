<?php

namespace EuaCreations\LaravelIam\Console;

use EuaCreations\LaravelIam\Support\FeatureSyncer;
use Illuminate\Console\Command;

class FeatureSyncCommand extends Command
{
    protected $signature = 'iam:sync-features {--dry-run : Show changes without writing}';
    protected $description = 'Discover features from routes and sync them into IAM';

    /**
     * Execute the command.
     */
    public function handle(): void
    {
        FeatureSyncer::sync($this, (bool) $this->option('dry-run'));
    }
}
