<?php

namespace Database\Seeders\Language;

use App\Models\Language\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->getAllLanguages() as $language) {
            Language::firstOrCreate($language);
        }
    }

    private function getAllLanguages(): array
    {
        return [
            [
                'code' => 'en',
                'name' => 'English',
                'native_name' => 'English',
            ],
            [
                'code' => 'ru',
                'name' => 'Russian',
                'native_name' => 'Русский',
            ],
        ];
    }
}
