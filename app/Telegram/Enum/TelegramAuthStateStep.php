<?php

namespace App\Telegram\Enum;

enum TelegramAuthStateStep: string
{
    case EMAIL = 'email';
    case TOKEN = 'token';
}
