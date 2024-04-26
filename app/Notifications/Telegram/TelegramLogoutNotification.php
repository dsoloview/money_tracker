<?php

namespace App\Notifications\Telegram;

use App\Notifications\Channels\TelegramChannel;

class TelegramLogoutNotification extends TelegramNotification
{
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
