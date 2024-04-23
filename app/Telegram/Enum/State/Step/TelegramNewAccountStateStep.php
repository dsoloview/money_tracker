<?php

namespace App\Telegram\Enum\State\Step;

enum TelegramNewAccountStateStep: string
{
    case NAME = 'name';
    case BANK = 'bank';
    case CURRENCY = 'currency';
    case BALANCE = 'balance';
}
