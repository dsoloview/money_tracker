<?php

namespace App\Telegram\Enum\Callback;

interface ICallbackType
{
    public function getMethodName(): string;
}
