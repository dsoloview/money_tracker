<?php

namespace App\Telegram\Controller;

use App\Models\Telegram\TelegramUser;
use App\Telegram\Intrerface\ITelegramController;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class MessageController implements ITelegramController
{
    public function process(Update $update, TelegramUser $telegramUser): void
    {
        $state = $telegramUser->state;

        if (!empty($state->state)) {
            $this->processState($update, $telegramUser);
            return;
        }

        $this->sendDefaultMessage($telegramUser);
    }

    private function processState(Update $update, TelegramUser $telegramUser): void
    {
        $state = $telegramUser->state->state;
        $controller = $state->getStateController();
        $controller->process($update, $telegramUser);
    }

    private function sendDefaultMessage(TelegramUser $telegramUser)
    {
        Telegram::sendMessage([
            'chat_id' => $telegramUser->chat_id,
            'text' => "Default message",
        ]);
    }
}
