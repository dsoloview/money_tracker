<?php

namespace App\ValueObjects;

use App\Models\Currency\Currency;

class UserCurrencyAmount
{
    public float $amount;
    public Currency $currency;

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): void
    {
        $this->currency = $currency;
    }


}
