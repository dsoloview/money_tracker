<?php

namespace App\Notifications\Telegram;

use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class TelegramStatisticsNotification extends AbstractTelegramNotification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): string
    {
        return TelegramChannel::class;
    }

    public function toTelegram(object $notifiable): array
    {
        return [
            'text' => \Blade::render('telegram.statistics'),
        ];
    }
}
