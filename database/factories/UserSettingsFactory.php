<?php

namespace Database\Factories;

use App\Models\Currency\Currency;
use App\Models\Language\Language;
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

            'main_currency_id' => Currency::inRandomOrder()?->first()?->id ?? Currency::factory(),
            'language_id' => Language::inRandomOrder()?->first()?->id ?? Language::factory(),
        ];
    }
}
