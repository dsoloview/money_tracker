<?php

namespace Database\Factories\Telegram;

use App\Models\Telegram\TelegramUserState;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TelegramUserStateFactory extends Factory
{
    protected $model = TelegramUserState::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id' => $this->faker->randomNumber(),
            'state' => $this->faker->word(),
        ];
    }
}
