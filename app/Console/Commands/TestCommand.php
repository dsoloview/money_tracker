<?php

namespace App\Console\Commands;

use App\Models\Transfer\Transfer;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test';

    protected $description = 'Command description';

    public function handle(): void
    {
        $transfer = Transfer::factory()->createOne();
        dd($transfer);
    }
}
