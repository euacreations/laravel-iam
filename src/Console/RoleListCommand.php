<?php

namespace EuaCreations\LaravelIam\Console;

use EuaCreations\LaravelIam\Support\RoleLister;
use Illuminate\Console\Command;

class RoleListCommand extends Command
{
    protected $signature = 'iam:role-list {--builtin : Only show built-in roles}';
    protected $description = 'List IAM roles';

    /**
     * Execute the command.
     */
    public function handle(): void
    {
        RoleLister::list($this, (bool) $this->option('builtin'));
    }
}
