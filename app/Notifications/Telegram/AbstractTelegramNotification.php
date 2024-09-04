<?php

namespace App\Notifications\Telegram;

use App\Notifications\Channels\TelegramChannel;
use Illuminate\Notifications\Notification;

abstract class AbstractTelegramNotification extends Notification
{
    public function viaQueues()
    {
        return ['notifications'];
    }

    public function via($notifiable): string
    {
        return TelegramChannel::class;
    }

    abstract public function toTelegram(object $notifiable): array;
}
