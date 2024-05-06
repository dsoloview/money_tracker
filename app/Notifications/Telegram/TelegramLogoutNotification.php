<?php

namespace App\Notifications\Telegram;

use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class TelegramLogoutNotification extends AbstractTelegramNotification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): string
    {
        return TelegramChannel::class;
    }

    public function toTelegram(object $notifiable): array
    {
        return [
            'text' => 'You have been logged out from Telegram by website.',
        ];
    }
}
