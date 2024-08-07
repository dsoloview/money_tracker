<?php

namespace App\Api\ExchangeRate\Implementations\ExchangeRateApi;

use App\Api\ExchangeRate\DTO\ExchangeRateDTO;
use App\Api\ExchangeRate\DTO\ExchangeRateResponse;
use App\Api\ExchangeRate\Interfaces\IExchangeRateMapper;

class ResponseMapper implements IExchangeRateMapper
{
    public static function map(array $data): ExchangeRateResponse
    {
        $exchangeRateResponse = new ExchangeRateResponse();
        $exchangeRates = [];

        foreach ($data as $currency => $rate) {
            $exchangeRate = new ExchangeRateDTO();
            $exchangeRate->setCurrency($currency);
            $exchangeRate->setRateToUsd($rate);
            $exchangeRates[] = $exchangeRate;
        }

        $exchangeRateResponse->setExchangeRates($exchangeRates);

        return $exchangeRateResponse;
    }
}
