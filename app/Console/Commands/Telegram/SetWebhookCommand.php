<?php

namespace App\Console\Commands\Telegram;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class SetWebhookCommand extends Command
{
    protected $signature = 'telegram:set-webhook';

    protected $description = 'Command description';

    public function handle(): void
    {
        $response = Telegram::setWebhook([
            'url' => route('telegram.webhook', ['token' => config('telegram.bots.mybot.token')]),
        ]);

        if ($response) {
            $this->info('Webhook set');
        } else {
            $this->error('Error setting webhook');
        }
    }
}
