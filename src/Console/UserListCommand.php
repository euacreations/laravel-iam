<?php

namespace EuaCreations\LaravelIam\Console;

use EuaCreations\LaravelIam\Support\UserLister;
use Illuminate\Console\Command;

class UserListCommand extends Command
{
    protected $signature = 'iam:user-list
                            {--role= : Filter by role (slug or id)}
                            {--user-field=username : Field to display (username|email|name)}';

    protected $description = 'List users with IAM roles';

    /**
     * Execute the command.
     */
    public function handle(): void
    {
        UserLister::list(
            $this,
            $this->option('role') ?: null,
            $this->option('user-field') ?: 'username'
        );
    }
}
