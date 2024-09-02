<?php

namespace Database\Seeders\Newsletter;

use App\Enums\Newsletter\NewslettersEnum;
use App\Models\Newsletter\Newsletter;
use Illuminate\Database\Seeder;

class NewsletterSeeder extends Seeder
{
    public function run(): void
    {
        foreach (NewslettersEnum::cases() as $newsletter) {
            Newsletter::updateOrCreate([
                'name' => $newsletter,
            ]);
        }
    }
}
