<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;

class TelegramChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        $message = $notification->toTelegram($notifiable);

        if ($notifiable instanceof AnonymousNotifiable) {
            $chatId = $notifiable->routes[self::class] ?? null;
        } else {
            $chatId = $notifiable->routeNotificationFor(self::class);
        }

        if (!$chatId) {
            return;
        }


        if (isset($message['document'])) {
            \Telegram::sendDocument([
                'chat_id' => $chatId,
                'text' => $message['text'],
                'document' => $message['document'],
            ]);
            return;

        }

        \Telegram::sendMessage([
            'chat_id' => $chatId,
            'text' => $message['text'],
        ]);
    }
}
