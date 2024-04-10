<?php

namespace Database\Factories\Telegram;

use App\Models\Telegram\TelegramToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TelegramTokenFactory extends Factory
{
    protected $model = TelegramToken::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'value' => $this->faker->word(),
            'user_id' => $this->faker->randomNumber(),
        ];
    }
}
