<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test:test';

    protected $description = 'Command description';

    public function handle(): void
    {
        $this->info('Test command');
    }
}
