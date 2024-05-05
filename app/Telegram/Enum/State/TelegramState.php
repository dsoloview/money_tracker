<?php

namespace App\Telegram\Enum\State;

use App\Telegram\Controller\Message\ImportController;
use App\Telegram\Controller\Message\NewAccountController;
use App\Telegram\Controller\Message\NewTransactionController;
use App\Telegram\Controller\Message\TelegramAuthController;
use App\Telegram\Intrerface\ITelegramController;

enum TelegramState: int
{
    case AUTH = 1;
    case NEW_TRANSACTION = 2;
    case NEW_ACCOUNT = 3;
    case IMPORT = 4;

    public function getStateController(): ITelegramController
    {
        return match ($this) {
            self::AUTH => app(TelegramAuthController::class),
            self::NEW_TRANSACTION => app(NewTransactionController::class),
            self::NEW_ACCOUNT => app(NewAccountController::class),
            self::IMPORT => app(ImportController::class),
            default => throw new \Exception('Unexpected match value'),
        };
    }
}
