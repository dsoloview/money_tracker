<?php

namespace Database\Seeders;

use Database\Seeders\Currency\CurrencySeeder;
use Database\Seeders\ExchangeRate\ExchangeRateSeeder;
use Database\Seeders\Icon\IconSeeder;
use Database\Seeders\Language\LanguageSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ExchangeRateSeeder::class,
            CurrencySeeder::class,
            LanguageSeeder::class,
            IconSeeder::class,
        ]);

        if (!app()->environment(['production', 'staging'])) {
            $this->call([
                ExchangeRateSeeder::class,
                RoleSeeder::class,
                UserSeeder::class,
            ]);
        }
    }
}
