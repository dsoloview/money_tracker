<?php

namespace App\Services\Currency;

use App\Models\ExchangeRate\ExchangeRate;
use App\Models\User;

class CurrencyConverterService
{
    public function convertToUserCurrency(float $amount, string $currency, User $user): float
    {
        $userCurrency = $user->currency->code;

        if ($userCurrency !== $currency) {
            $amount = $this->convert($amount, $currency, $userCurrency);
        }

        return $amount;
    }

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
        /** ?? 1 is a hack for scribe docs generation */
        return \Cache::remember(
            "exchange_rate_{$currency}",
            now()->addHours(12),
            function () use ($currency) {
                return ExchangeRate::where('currency', $currency)->first()->rate_to_usd ?? 1;
            }
        );
    }
}
