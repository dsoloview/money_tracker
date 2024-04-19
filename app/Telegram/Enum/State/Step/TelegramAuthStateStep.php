<?php

namespace App\Telegram\Enum\State\Step;

enum TelegramAuthStateStep: string
{
    case EMAIL = 'email';
    case TOKEN = 'token';
}
