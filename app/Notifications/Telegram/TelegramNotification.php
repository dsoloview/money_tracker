<?php

namespace App\Notifications\Telegram;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

abstract class TelegramNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function viaQueues()
    {
        return ['notifications'];
    }

    abstract public function toTelegram(object $notifiable): array;
}
