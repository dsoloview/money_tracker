<?php

namespace Database\Seeders;

use Database\Seeders\Currency\CurrencySeeder;
use Database\Seeders\ExchangeRate\ExchangeRateSeeder;
use Database\Seeders\Icon\IconSeeder;
use Database\Seeders\Language\LanguageSeeder;
use Database\Seeders\Newsletter\NewsletterChannelSeeder;
use Database\Seeders\Newsletter\NewslettersAvailableChannelSeeder;
use Database\Seeders\Newsletter\NewsletterSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            NewsletterSeeder::class,
            NewsletterChannelSeeder::class,
            NewslettersAvailableChannelSeeder::class
        ]);

        $this->call([
            ExchangeRateSeeder::class,
            CurrencySeeder::class,
            LanguageSeeder::class,
            IconSeeder::class,
            RoleSeeder::class,
        ]);

        if (!app()->environment(['production', 'staging', 'testing'])) {
            $this->call([
                UserSeeder::class,
            ]);
        }
    }
}
