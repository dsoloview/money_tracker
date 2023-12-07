<?php

namespace Database\Seeders;

use App\Enums\Role\Roles;
use App\Models\Currency\Currency;
use App\Models\Language\Language;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['production', 'staging'])) {
            $this->createSuperadmin();
        }

        User::factory()->count(10)->create();
    }

    private function createSuperadmin(): void
    {
        $superadmin = User::firstOrCreate([
            'email' => 'superadmin@superadmin.com',
        ], [
            'name' => 'Superadmin',
            'email' => 'superadmin@superadmin.com',
            'password' => 'password',
        ]);

        $superadmin->assignRole(Roles::admin);
        $superadmin->settings()->create(
            [
                'main_currency_id' => Currency::where('code', 'EUR')->first()->id,
                'language_id' => Language::where('code', 'ru')->first()->id,
            ]
        );
    }
}
