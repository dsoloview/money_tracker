<?php

namespace App\Api\ExchangeRate\DTO;

class ExchangeRateResponse
{
    /**
     * @return ExchangeRateDTO[]
     */
    private array $exchangeRates;

    /**
     * @return ExchangeRateDTO[]
     */
    public function getExchangeRates(): array
    {
        return $this->exchangeRates;
    }

    /**
     * @param  ExchangeRateDTO[]  $exchangeRates
     */
    public function setExchangeRates(array $exchangeRates): void
    {
        $this->exchangeRates = $exchangeRates;
    }
}
