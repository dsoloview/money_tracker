<?php

namespace App\Console\Commands;

use App\Interfaces\IExchangeRateFetcher;
use App\Models\ExchangeRate\ExchangeRate;
use Illuminate\Console\Command;
use Log;

class FetchExchangeRatesCommand extends Command
{
    protected $signature = 'fetch:exchange-rates';

    protected $description = 'Command description';

    public function handle(): void
    {
        Log::info('Fetching exchange rates');
        $fetcher = app(IExchangeRateFetcher::class);

        $exchangeRates = $fetcher->getExchangeRatesForUSD();

        foreach ($exchangeRates as $currency => $rate) {
            ExchangeRate::updateOrCreate(
                ['currency' => $currency],
                ['rate_to_usd' => $rate]
            );
        }

        \Cache::tags(['exchange_rate'])->flush();

        Log::info('Exchange rates fetched');
    }
}
