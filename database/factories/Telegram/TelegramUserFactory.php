<?php

namespace Database\Factories\Telegram;

use App\Models\Telegram\TelegramUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TelegramUserFactory extends Factory
{
    protected $model = TelegramUser::class;

    public function definition(): array
    {
        $telegramId = $this->faker->unique()->randomNumber();
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'telegram_id' => $telegramId,
            'username' => $this->faker->unique()->userName(),
            'user_id' => User::factory(),
        ];
    }
}
