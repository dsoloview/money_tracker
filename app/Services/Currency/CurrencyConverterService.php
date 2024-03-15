<?php

namespace App\Services\Currency;

use App\Models\ExchangeRate\ExchangeRate;

class CurrencyConverterService
{
    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        $exchangeRate = $this->getExchangeRate($fromCurrency, $toCurrency);

        return round($amount * $exchangeRate, 2);
    }

    private function getExchangeRate(string $fromCurrency, string $toCurrency): float
    {
        $rateFromUsd = $this->getExchangeRateByCurrency($fromCurrency);
        $rateToUsd = $this->getExchangeRateByCurrency($toCurrency);

        return $rateToUsd / $rateFromUsd;
    }

    private function getExchangeRateByCurrency(string $currency): float
    {
        return \Cache::remember(
            "exchange_rate_{$currency}",
            now()->addHours(12),
            function () use ($currency) {
                return ExchangeRate::where('currency', $currency)->first()->rate_to_usd;
            }
        );
    }
}
