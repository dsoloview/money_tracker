<?php

namespace Database\Seeders;

use Database\Seeders\Newsletter\NewsletterChannelSeeder;
use Database\Seeders\Newsletter\NewslettersAvailableChannelSeeder;
use Database\Seeders\Newsletter\NewsletterSeeder;
use Illuminate\Database\Seeder;

class DeploySeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            NewsletterSeeder::class,
            NewsletterChannelSeeder::class,
            NewslettersAvailableChannelSeeder::class,
        ]);
    }
}
