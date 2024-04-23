<?php

namespace App\Telegram\Enum\Callback;

use App\Telegram\Controller\Callback\NewAccountCallbackController;
use App\Telegram\Controller\Callback\NewTransactionCallbackController;
use App\Telegram\Controller\Callback\TransactionCallbackController;
use App\Telegram\Enum\Callback\Account\CallbackNewAccountGroupType;
use App\Telegram\Enum\Callback\Transaction\CallbackNewTransactionGroupType;
use App\Telegram\Enum\Callback\Transaction\CallbackTransactionGroupType;
use App\Telegram\Intrerface\ITelegramController;

enum CallbackGroup: int
{
    case TRANSACTIONS = 1;
    case NEW_TRANSACTION = 2;
    case NEW_ACCOUNT = 3;

    public function getCallbackController(): ITelegramController
    {
        return match ($this) {
            self::TRANSACTIONS => app(TransactionCallbackController::class),
            self::NEW_TRANSACTION => app(NewTransactionCallbackController::class),
            self::NEW_ACCOUNT => app(NewAccountCallbackController::class),
            default => throw new \Exception('Unexpected match value'),
        };
    }

    public function getCallbackType(int $type): ICallbackType
    {
        return match ($this) {
            self::TRANSACTIONS => CallbackTransactionGroupType::from($type),
            self::NEW_TRANSACTION => CallbackNewTransactionGroupType::from($type),
            self::NEW_ACCOUNT => CallbackNewAccountGroupType::from($type),
            default => throw new \Exception('Unexpected match value'),
        };
    }
}
