<?php

namespace App\Interfaces;

interface IExchangeRateFetcher
{
    public function getExchangeRatesForUSD(): array;
}
