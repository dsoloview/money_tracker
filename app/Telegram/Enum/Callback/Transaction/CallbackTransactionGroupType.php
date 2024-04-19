<?php

namespace App\Telegram\Enum\Callback\Transaction;

use App\Telegram\Enum\Callback\ICallbackType;

enum CallbackTransactionGroupType: int implements ICallbackType
{
    case PAGINATION = 1;

    public function getMethodName(): string
    {
        return \Str::camel(strtolower($this->name));
    }
}
