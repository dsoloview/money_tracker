<?php

namespace Database\Factories\Telegram;

use App\Models\Telegram\TelegramUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TelegramUserFactory extends Factory
{
    protected $model = TelegramUser::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'telegram_id' => $this->faker->word(),
            'username' => $this->faker->userName(),
            'user_id' => $this->faker->randomNumber(),
        ];
    }
}
