<?php

namespace App\Api\ExchangeRate\Implementations\ExchangeRateApi;

use App\Api\ExchangeRate\DTO\ExchangeRateResponse;
use App\Api\ExchangeRate\Interfaces\IExchangeRateFetcher;
use App\Api\ExchangeRate\Interfaces\IExchangeRateMapper;

class ExchangeRateApi implements IExchangeRateFetcher
{
    private const string API_URL = 'https://v6.exchangerate-api.com/v6';

    private const string EXCHANGE_RATES_ENDPOINT = 'latest';

    private const string SUPPORTED_CURRENCIES_ENDPOINT = 'codes';

    private const string BASE_CURRENCY = 'USD';

    private IExchangeRateMapper $mapper;

    public function __construct(IExchangeRateMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    public function getExchangeRatesForUSD(): ExchangeRateResponse
    {

        $query = self::API_URL.'/'.config('services.api.exchange_rate_api').'/'.self::EXCHANGE_RATES_ENDPOINT.'/'.self::BASE_CURRENCY;
        $response = \Http::get($query);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch exchange rates');
        }

        $data = $response->json();

        return $this->mapper->map($data['conversion_rates']);
    }
}
