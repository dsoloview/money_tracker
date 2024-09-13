<?php

namespace App\Console\Commands;

use App\Imports\ZenMoneyImport;
use App\Models\User;
use App\Telegram\Enum\Import\ImportMode;
use Illuminate\Console\Command;
use Storage;

class TestImportCommand extends Command
{
    protected $signature = 'test:import';

    protected $description = 'Command description';

    public function handle(): void
    {
        $path = Storage::path('/imports/full.csv');

        \Excel::import(new ZenMoneyImport(ImportMode::CREATE_ABSENT_ENTITIES, User::find(1)), $path);
    }
}
