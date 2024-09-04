<?php

namespace App\Notifications\Telegram;

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

    public function toTelegram($notifiable): array
    {
        return [
            'text' => $this->success ? 'Export successful' : 'Export failed',
            'document' => $this->inputFile,
        ];
    }
}
