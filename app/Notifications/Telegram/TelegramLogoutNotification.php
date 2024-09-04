<?php

namespace App\Notifications\Telegram;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class TelegramLogoutNotification extends AbstractTelegramNotification implements ShouldQueue
{
    use Queueable;

    public function toTelegram(object $notifiable): array
    {
        return [
            'text' => 'You have been logged out from Telegram by website.',
        ];
    }
}
