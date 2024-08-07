<?php

namespace App\Api\ExchangeRate\Interfaces;

use App\Api\ExchangeRate\DTO\ExchangeRateResponse;

interface IExchangeRateFetcher
{
    public function getExchangeRatesForUSD(): ExchangeRateResponse;
}
