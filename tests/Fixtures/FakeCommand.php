<?php

namespace Tests\Fixtures;

use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class FakeCommand extends Command
{
    protected $signature = 'test:fake-command';
    protected $description = 'Fake command for testing';

    public function __construct()
    {
        parent::__construct();
        $this->setOutput(new OutputStyle(new ArrayInput([]), new NullOutput()));
    }

    public function handle(): void
    {
    }
}
