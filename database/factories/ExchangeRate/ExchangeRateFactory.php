<?php

namespace Database\Factories\ExchangeRate;

use App\Models\ExchangeRate\ExchangeRate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ExchangeRateFactory extends Factory
{
    protected $model = ExchangeRate::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'currency' => $this->faker->word(),
            'rate_to_usd' => $this->faker->randomFloat(),
        ];
    }
}
