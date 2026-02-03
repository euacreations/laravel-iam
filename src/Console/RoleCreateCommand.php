<?php

namespace EuaCreations\LaravelIam\Console;

use EuaCreations\LaravelIam\Support\RoleCreator;
use Illuminate\Console\Command;

class RoleCreateCommand extends Command
{
    protected $signature = 'iam:role-create
                            {slug : Unique slug for the role}
                            {name : Display name for the role}
                            {--auto-assign : Auto-assign new features}
                            {--builtin : Mark role as built-in}';

    protected $description = 'Create a new role';

    /**
     * Execute the command.
     */
    public function handle(): void
    {
        RoleCreator::create(
            $this,
            $this->argument('slug'),
            $this->argument('name'),
            (bool) $this->option('auto-assign'),
            (bool) $this->option('builtin')
        );
    }
}
