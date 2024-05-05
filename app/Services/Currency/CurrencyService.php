<?php

namespace App\Services\Currency;

use App\Models\Currency\Currency;
use Illuminate\Support\Collection;

class CurrencyService
{
    public function index(): Collection
    {
        return \Cache::tags(['currencies'])->remember('currencies', 60 * 60 * 24, function () {
            return Currency::all();
        });
    }

    public function getCurrencyByCode(string $currencyCode): ?Currency
    {
        return \Cache::tags(['currencies'])->remember("currency_{$currencyCode}", 60 * 60 * 24,
            function () use ($currencyCode) {
                return Currency::where('code', $currencyCode)->first();
            });
    }
}
