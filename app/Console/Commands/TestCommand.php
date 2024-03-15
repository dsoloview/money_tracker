<?php

namespace App\Console\Commands;

use App\Services\Currency\CurrencyConverterService;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test';

    protected $description = 'Command description';

    public function handle(): void
    {
        $converter = new CurrencyConverterService();
        $res = $converter->convert(100, 'TRY', 'EUR');

        $this->info($res);
    }
}
