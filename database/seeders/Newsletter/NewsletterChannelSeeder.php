<?php

namespace Database\Seeders\Newsletter;

use App\Enums\Newsletter\NewsletterChannelsEnum;
use App\Models\Newsletter\NewsletterChannel;
use Illuminate\Database\Seeder;

class NewsletterChannelSeeder extends Seeder
{
    public function run(): void
    {
        foreach (NewsletterChannelsEnum::cases() as $channel) {
            NewsletterChannel::updateOrCreate([
                'name' => $channel,
            ]);
        }
    }
}
