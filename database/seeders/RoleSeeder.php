<?php

namespace Database\Seeders;

use App\Enums\Role\Roles;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Roles::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value]);
        }
    }
}
