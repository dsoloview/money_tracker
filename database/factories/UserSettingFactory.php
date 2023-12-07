<?php

namespace Database\Factories;

use App\Models\Currency\Currency;
use App\Models\Language\Language;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserSettingFactory extends Factory
{
    protected $model = UserSetting::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->id,
            'main_currency_id' => Currency::factory()->create()->id,
            'language_id' => Language::factory()->create()->id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
