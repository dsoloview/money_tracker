<?php

namespace App\Api\ExchangeRate\Interfaces;

use App\Api\ExchangeRate\DTO\ExchangeRateResponse;

interface IExchangeRateMapper
{
    public static function map(array $data): ExchangeRateResponse;
}
