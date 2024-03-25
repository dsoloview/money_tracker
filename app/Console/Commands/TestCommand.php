<?php

namespace App\Console\Commands;

use Database\Seeders\Icon\IconSeeder;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test';

    protected $description = 'Command description';

    public function handle(): void
    {
        $seeder = new IconSeeder();
        $seeder->run();
    }
}
