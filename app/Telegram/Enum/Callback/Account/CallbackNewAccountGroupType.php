<?php

namespace App\Telegram\Enum\Callback\Account;

use App\Telegram\Enum\Callback\ICallbackType;

enum CallbackNewAccountGroupType: int implements ICallbackType
{
    case CURRENCY = 1;

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
