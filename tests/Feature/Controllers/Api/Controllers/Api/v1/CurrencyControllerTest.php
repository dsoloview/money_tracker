<?php

namespace Tests\Feature\Controllers\Api\Controllers\Api\v1;

use App\Models\Currency\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrencyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetAllCurrencies()
    {
        Currency::factory()->count(5)->create();

        $response = $this->getJson(route('currencies.index'));

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'code',
                        'symbol',
                    ]
                ]
            ]);
    }

    public function testShowCurrency()
    {
        $currency = Currency::factory()->create();

        $response = $this->getJson(route('currencies.show', $currency->id));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $currency->id,
                    'name' => $currency->name,
                    'code' => $currency->code,
                    'symbol' => $currency->symbol,
                ]
            ]);
    }
}
