<?php

namespace App\Telegram\Intrerface;

use Telegram\Bot\Objects\Update;

interface ITelegramController
{
    public function process(Update $update): void;
}
