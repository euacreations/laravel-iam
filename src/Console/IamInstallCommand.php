<?php

namespace EuaCreations\LaravelIam\Console;

use EuaCreations\LaravelIam\Support\IamInstaller;
use Illuminate\Console\Command;

class IamInstallCommand extends Command
{
    protected $signature = 'iam:install';
    protected $description = 'Install IAM: create built-in roles';

    /**
     * Execute the command.
     */
    public function handle(): void
    {
        IamInstaller::install($this);
    }
}
