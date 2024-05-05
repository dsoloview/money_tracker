<?php

namespace App\Notifications\Telegram;

use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;

class TelegramImportNotification extends TelegramNotification
{
    use Queueable;

    private bool $success;

    public function __construct(bool $success)
    {
        $this->success = $success;
    }

    public function via($notifiable): string
    {
        return TelegramChannel::class;
    }

    public function toTelegram($notifiable): array
    {
        return [
            'text' => $this->success ? 'Import successful' : 'Import failed',
        ];
    }
}
