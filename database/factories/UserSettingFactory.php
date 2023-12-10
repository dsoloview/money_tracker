<?php

namespace Database\Factories;

use App\Models\Currency\Currency;
use App\Models\Language\Language;
use App\Models\UserSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class UserSettingFactory extends Factory
{
    protected $model = UserSetting::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'main_currency_id' => Currency::inRandomOrder()->first()->id,
            'language_id' => Language::inRandomOrder()->first()->id,
        ];
    }
}
