<?php

namespace EuaCreations\LaravelIam\Console;

use EuaCreations\LaravelIam\Support\RoleShower;
use Illuminate\Console\Command;

class RoleShowCommand extends Command
{
    protected $signature = 'iam:role-show
                            {role : Role identifier (slug or id)}
                            {--role-field=slug : Field to match role (slug|id|name)}';

    protected $description = 'Show role details and its features';

    /**
     * Execute the command.
     */
    public function handle(): void
    {
        RoleShower::show(
            $this,
            $this->argument('role'),
            $this->option('role-field')
        );
    }
}
