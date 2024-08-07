<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CustomMigrateCommand extends Command
{
    protected $signature = 'custom:migrate';

    protected $description = 'Command description';

    public function handle(): void
    {
        $this->call('migrate', [
            '--path' => 'database/customMigrations',
        ]);
    }
}
