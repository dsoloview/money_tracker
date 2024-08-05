<?php

namespace Tests\Unit\Services\Currency;

use App\Models\Currency\Currency;
use App\Models\User;
use App\Services\Currency\CurrencyConverterService;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class CurrencyConverterServiceTest extends TestCase
{
    public function testConvertToUserCurrencyConvertsAmountCorrectly()
    {
        $usdCurrency = Currency::factory()->make([
            'id' => 1,
            'code' => 'USD'
        ]);

        $user = User::factory()->make();
        $user->currency = $usdCurrency;

        $amount = 100;
        $expectedAmount = 85;

        $currencyConverterService = Mockery::mock(CurrencyConverterService::class)->makePartial();
        $currencyConverterService->shouldReceive('convert')
            ->with($amount, 'EUR', 'USD')
            ->andReturn($expectedAmount);

        $convertedAmount = $currencyConverterService->convertToUserCurrency($amount, 'EUR', $user);

        $this->assertEquals($expectedAmount, $convertedAmount);
    }

    public function testConvertCalculatesAmountWithCorrectExchangeRate()
    {
        $amount = 150;
        $fromCurrency = 'GBP';
        $toCurrency = 'USD';

        $exchangeRate = 1.25;
        $expectedAmount = round($amount * $exchangeRate, 2);

        Cache::shouldReceive('tags')
            ->with(['exchange_rate'])
            ->andReturnSelf();

        Cache::shouldReceive('rememberForever')
            ->once()
            ->with("exchange_rate_{$fromCurrency}", Mockery::type('Closure'))
            ->andReturn(1);

        Cache::shouldReceive('rememberForever')
            ->once()
            ->with("exchange_rate_{$toCurrency}", Mockery::type('Closure'))
            ->andReturn($exchangeRate);

        $currencyConverterService = new CurrencyConverterService();

        $convertedAmount = $currencyConverterService->convert($amount, $fromCurrency, $toCurrency);

        $this->assertEquals($expectedAmount, $convertedAmount);
    }

    public function testGetExchangeRateReturnsCorrectRate()
    {
        $fromCurrency = 'EUR';
        $toCurrency = 'JPY';

        $rateFromUsd = 0.85; // 1 EUR = 0.85 USD
        $rateToUsd = 0.009; // 1 JPY = 0.009 USD
        $expectedExchangeRate = round($rateToUsd / $rateFromUsd, 2);

        Cache::shouldReceive('tags')
            ->with(['exchange_rate'])
            ->andReturnSelf();

        Cache::shouldReceive('rememberForever')
            ->with("exchange_rate_EUR", Mockery::type('Closure'))
            ->andReturn($rateFromUsd);

        Cache::shouldReceive('rememberForever')
            ->with("exchange_rate_JPY", Mockery::type('Closure'))
            ->andReturn($rateToUsd);

        $currencyConverterService = new CurrencyConverterService();

        $exchangeRate = $currencyConverterService->convert(1, $fromCurrency, $toCurrency);

        $this->assertEquals($expectedExchangeRate, $exchangeRate);
    }
}
