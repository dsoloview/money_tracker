<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Currency\CurrencySeeder;
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
            CurrencySeeder::class,
            LanguageSeeder::class,
        ]);

        if (! app()->environment(['production', 'staging'])) {
            $this->call([
                RoleSeeder::class,
                UserSeeder::class,
            ]);
        }
    }
}
