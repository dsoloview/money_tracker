<?php

namespace Database\Seeders;

use App\Enums\Role\Roles;
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
            'email' => 'superadmin@siperadmin.com',
        ], [
            'name' => 'Superadmin',
            'email' => 'superadmin@siperadmin.com',
            'password' => 'password',
        ]);

        $superadmin->assignRole(Roles::admin);
    }
}
