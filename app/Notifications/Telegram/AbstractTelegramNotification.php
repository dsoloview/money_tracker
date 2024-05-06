<?php

namespace App\Notifications\Telegram;

use Illuminate\Notifications\Notification;

abstract class AbstractTelegramNotification extends Notification
{
    public function viaQueues()
    {
        return ['notifications'];
    }

    abstract public function toTelegram(object $notifiable): array;
}
