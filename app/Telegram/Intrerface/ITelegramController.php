<?php

namespace App\Telegram\Intrerface;

use App\Models\Telegram\TelegramUser;
use Telegram\Bot\Objects\Update;

interface ITelegramController
{
    public function process(Update $update, TelegramUser $telegramUser): void;
}
