<?php

namespace App\Telegram\Controller;

use App\Telegram\Facades\TgUser;
use App\Telegram\Intrerface\ITelegramController;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class MessageController implements ITelegramController
{
    public function process(Update $update): void
    {
        if (TgUser::hasState()) {
            $this->processState($update);

            return;
        }

        $this->sendDefaultMessage();
    }

    private function processState(Update $update): void
    {
        $state = TgUser::state()->state;
        $controller = $state->getStateController();
        $controller->process($update);
    }

    private function sendDefaultMessage(): void
    {
        Telegram::sendMessage([
            'chat_id' => TgUser::chatId(),
            'text' => 'Default message',
        ]);
    }
}
