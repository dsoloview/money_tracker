<?php

namespace App\Telegram\Enum;

use App\Telegram\Controller\TelegramAuthController;
use App\Telegram\Intrerface\ITelegramController;

enum TelegramState: string
{
    case AUTH = 'auth';

    public function getStateController(): ITelegramController
    {
        return match ($this) {
            self::AUTH => app(TelegramAuthController::class),
            default => throw new \Exception('Unexpected match value'),
        };
    }
}
