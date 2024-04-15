<?php

namespace App\Api\ExchangeRate;

use App\Interfaces\IExchangeRateFetcher;

class ExchangeRateApi implements IExchangeRateFetcher
{
    private const API_URL = 'https://v6.exchangerate-api.com/v6';

    private const EXCHANGE_RATES_ENDPOINT = 'latest';

    private const SUPPORTED_CURRENCIES_ENDPOINT = 'codes';

    private const BASE_CURRENCY = 'USD';

    public function getExchangeRatesForUSD(): array
    {


        $query = self::API_URL.'/'.config('services.api.exchange_rate_api').'/'.self::EXCHANGE_RATES_ENDPOINT.'/'.self::BASE_CURRENCY;
        $response = \Http::get($query);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch exchange rates');
        }

        $data = $response->json();

        return $data['conversion_rates'];
    }

    public function getAvailableCurrencies(): array
    {
        $query = self::API_URL.'/'.config('services.api.exchange_rate_api').'/'.self::SUPPORTED_CURRENCIES_ENDPOINT;
        $response = \Http::get($query, [
            'apikey' => config('services.api.free_currency_converter_api_key'),
        ]);

        return $response->json();
    }
}
