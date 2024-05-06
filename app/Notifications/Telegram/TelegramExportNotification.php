<?php

namespace App\Notifications\Telegram;

use App\Notifications\Channels\TelegramChannel;
use Telegram\Bot\FileUpload\InputFile;

class TelegramExportNotification extends AbstractTelegramNotification
{
    private bool $success;
    private ?InputFile $inputFile;

    public function __construct(bool $success, ?InputFile $inputFile = null)
    {
        $this->success = $success;
        $this->inputFile = $inputFile;
    }

    public function via($notifiable): string
    {
        return TelegramChannel::class;
    }

    public function toTelegram($notifiable): array
    {
        return [
            'text' => $this->success ? 'Export successful' : 'Export failed',
            'document' => $this->inputFile,
        ];
    }
}
