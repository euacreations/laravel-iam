<?php

namespace EuaCreations\LaravelIam\Console;

use EuaCreations\LaravelIam\Support\RoleAssigner;
use Illuminate\Console\Command;

class RoleAssignCommand extends Command
{
    protected $signature = 'iam:role-assign
                            {user : User identifier (id or username)}
                            {role : Role identifier (slug or id)}
                            {--user-field=id : Field to match user (id|username|email)}
                            {--role-field=slug : Field to match role (slug|id|name)}';

    protected $description = 'Assign a role to a user';

    /**
     * Execute the command.
     */
    public function handle(): void
    {
        $userValue = $this->argument('user');
        $roleValue = $this->argument('role');
        $userField = $this->option('user-field');
        $roleField = $this->option('role-field');

        RoleAssigner::assign($this, $userValue, $roleValue, $userField, $roleField);
    }
}
