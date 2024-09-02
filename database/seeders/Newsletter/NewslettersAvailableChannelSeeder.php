<?php

namespace Database\Seeders\Newsletter;

use Illuminate\Database\Seeder;

class NewslettersAvailableChannelSeeder extends Seeder
{
    public function run()
    {
        $newsletters = \App\Models\Newsletter\Newsletter::all();
        $channels = \App\Models\Newsletter\NewsletterChannel::all();

        $newsletters->each(function ($newsletter) use ($channels) {
            $newsletter->availableChannels()->sync($channels->pluck('id'));
        });
    }
}
