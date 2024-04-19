<?php

namespace App\Telegram\Enum\Callback\Transaction;

use App\Telegram\Enum\Callback\ICallbackType;

enum CallbackNewTransactionGroupType: int implements ICallbackType
{
    case ACCOUNT = 1;
    case TYPE = 2;
    case CATEGORY = 3;
    case CATEGORY_DONE = 4;

    public function getMethodName(): string
    {
        return \Str::camel(strtolower($this->name));
    }

    public static function getMethods(): array
    {
        $methods = [];

        foreach (self::cases() as $case) {
            $methods[] = $case->getMethodName();
        }

        return $methods;
    }
}
