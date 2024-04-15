<?php

namespace App\Console\Commands\Telegram;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class RemoveWebhookCommand extends Command
{
    protected $signature = 'telegram:remove-webhook';

    protected $description = 'Command description';

    public function handle(): void
    {
        $response = Telegram::removeWebhook();

        if ($response) {
            $this->info('Webhook removed');
        } else {
            $this->error('Error removing webhook');
        }
    }
}
