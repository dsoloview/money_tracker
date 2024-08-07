<?php

namespace Tests\Unit\Services\Currency;

use App\Models\Currency\Currency;
use App\Services\Currency\CurrencyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CurrencyServiceTest extends TestCase
{
    use RefreshDatabase;

    private CurrencyService $currencyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currencyService = app(CurrencyService::class);
    }

    public function testIndexFetchesAllCurrencies()
    {
        Currency::factory()->count(5)->create();

        $currencies = $this->currencyService->index();

        $this->assertInstanceOf(Collection::class, $currencies);
        $this->assertCount(5, $currencies);
    }

    public function testIndexUsesCache()
    {
        $currencies = Currency::factory()->count(5)->create();

        Cache::shouldReceive('tags')->once()->with(['currencies'])->andReturnSelf();
        Cache::shouldReceive('remember')->once()->with('currencies', 60 * 60 * 24, \Closure::class)
            ->andReturn($currencies);

        $currencies = $this->currencyService->index();

        $this->assertInstanceOf(Collection::class, $currencies);
        $this->assertCount(5, $currencies);
        $this->assertEquals($currencies, $currencies);
    }

    public function testGetCurrencyByCodeReturnsCurrency()
    {
        $currency = Currency::factory()->create(['code' => 'USD']);

        $result = $this->currencyService->getCurrencyByCode('USD');

        $this->assertInstanceOf(Currency::class, $result);
        $this->assertEquals('USD', $result->code);
    }

    public function testGetCurrencyByCodeUsesCache()
    {
        $currencyCode = 'USD';

        $currency = Currency::factory()->create(['code' => $currencyCode]);
        Cache::shouldReceive('tags')->once()->with(['currencies'])->andReturnSelf();
        Cache::shouldReceive('remember')->once()->with("currency_{$currencyCode}", 60 * 60 * 24, \Closure::class)
            ->andReturn($currency);

        $result = $this->currencyService->getCurrencyByCode($currencyCode);

        $this->assertInstanceOf(Currency::class, $result);
        $this->assertEquals('USD', $result->code);
    }

    public function testGetCurrencyByCodeReturnsNullForNonExistentCode()
    {
        $result = $this->currencyService->getCurrencyByCode('NON_EXISTENT');

        $this->assertNull($result);
    }
}
