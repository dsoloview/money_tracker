<?php

namespace Database\Factories\Telegram;

use App\Models\Telegram\TelegramUser;
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
            'telegram_user_id' => TelegramUser::factory(),
            'state' => $this->faker->word(),
        ];
    }
}
