<?php

namespace EuaCreations\LaravelIam\Console;

use Illuminate\Console\Command;
use EuaCreations\LaravelIam\Support\IamInstaller;

class IamInstallCommand extends Command
{
    protected $signature = 'iam:install';
    protected $description = 'Install IAM: create built-in roles';

    public function handle(): void
    {
        $this->info('Installing IAM package...');
        IamInstaller::ensureBuiltinRoles();
        $this->info('Built-in roles created successfully!');
    }
}
