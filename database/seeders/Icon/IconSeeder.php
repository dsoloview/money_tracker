<?php

namespace Database\Seeders\Icon;

use App\Models\Icon\Icon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class IconSeeder extends Seeder
{
    public function run(): void
    {
        $icons = Storage::disk('public')->allFiles('icons');

        foreach ($icons as $icon) {
            $name = pathinfo($icon, PATHINFO_FILENAME);
            $path = Storage::url($icon);

            Icon::updateOrCreate(
                [
                    'path' => $path,
                ],
                [
                    'name' => $name,
                ]
            );
        }
    }
}
