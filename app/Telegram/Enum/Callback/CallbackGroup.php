<?php

namespace App\Telegram\Enum\Callback;

use App\Telegram\Controller\Callback\TransactionCallbackController;
use App\Telegram\Intrerface\ITelegramController;

enum CallbackGroup: string
{
    case TRANSACTIONS = 'transactions';

    public function getCallbackController(): ITelegramController
    {
        return match ($this) {
            self::TRANSACTIONS => app(TransactionCallbackController::class),
            default => throw new \Exception('Unexpected match value'),
        };
    }
}
