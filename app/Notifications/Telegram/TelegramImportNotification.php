<?php

namespace App\Notifications\Telegram;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class TelegramImportNotification extends AbstractTelegramNotification implements ShouldQueue
{
    use Queueable;

    private bool $success;

    public function __construct(bool $success)
    {
        $this->success = $success;
    }

    public function toTelegram($notifiable): array
    {
        return [
            'text' => $this->success ? 'Import successful' : 'Import failed',
        ];
    }
}
