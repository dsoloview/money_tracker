<?php

namespace App\Telegram\Controller;

use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class MessageController
{
    public function process(Update $update): void
    {
        Telegram::sendMessage([
            'chat_id' => $update->getChat()->getId(),
            'text' => 'Message controller!'
        ]);
    }
}
