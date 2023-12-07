<?php

namespace App\Services\Currency;

use App\Models\Currency\Currency;
use Illuminate\Support\Collection;

class CurrencyService
{
    public function index(): Collection
    {
        return \Cache::remember('currencies', 60 * 60 * 24, function () {
            return Currency::all();
        });
    }
}
