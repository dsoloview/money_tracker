<?php

namespace Database\Factories;

use App\Models\Currency\Currency;
use App\Models\Language\Language;
use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserSettingsFactory extends Factory
{
    protected $model = UserSettings::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
            'main_currency_id' => Currency::factory(),
            'language_id' => Language::factory(),
        ];
    }
}
