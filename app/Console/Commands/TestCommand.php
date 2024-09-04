<?php

namespace App\Console\Commands;

use App\Enums\Newsletter\NewsletterPeriodsEnum;
use App\Services\Newsletter\Sender\NewsletterSender;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    protected $signature = 'test:test';

    protected $description = 'Command description';

    public function handle(): void
    {
        $sender = app(NewsletterSender::class);
        $sender->send(NewsletterPeriodsEnum::DAILY);
    }
}
