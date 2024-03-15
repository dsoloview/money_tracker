<?php

namespace App\Console\Commands;

use App\Interfaces\IExchangeRateFetcher;
use App\Models\ExchangeRate\ExchangeRate;
use Illuminate\Console\Command;

class FetchExchangeRatesCommand extends Command
{
    protected $signature = 'fetch:exchange-rates';

    protected $description = 'Command description';

    public function handle(): void
    {
        $fetcher = app(IExchangeRateFetcher::class);

        $exchangeRates = $fetcher->getExchangeRatesForUSD();

        foreach ($exchangeRates as $currency => $rate) {
            ExchangeRate::updateOrCreate(
                ['currency' => $currency],
                ['rate_to_usd' => $rate]
            );
        }
    }
}
