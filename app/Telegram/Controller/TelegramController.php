<?php

namespace App\Telegram\Controller;

use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class TelegramController
{
    public function process(Update $update): void
    {
        $type = $update->objectType();

        if ($type === 'message' && str_starts_with($update->getMessage()->getText(), '/')) {
            $this->processCommand($update);
            return;
        }

        if ($type === 'message') {
            $this->processMessage($update);
            return;
        }

    }

    private function processMessage(Update $update): void
    {
        $messageController = new MessageController();
        $messageController->process($update);
    }

    private function processCommand(Update $update): void
    {
        Telegram::processCommand($update);
    }


}
