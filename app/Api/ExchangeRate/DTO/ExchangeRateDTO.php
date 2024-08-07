<?php

namespace App\Api\ExchangeRate\DTO;

class ExchangeRateDTO
{
    private string $currency;
    private float $rateToUsd;

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getRateToUsd(): float
    {
        return $this->rateToUsd;
    }

    public function setRateToUsd(float $rateToUsd): void
    {
        $this->rateToUsd = $rateToUsd;
    }


}
