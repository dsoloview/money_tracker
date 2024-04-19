<?php

namespace App\Telegram\Enum\State\Step;

enum TelegramNewTransactionStateStep: string
{
    case AMOUNT = 'amount';
    case COMMENT = 'comment';
    case DATE = 'date';
}
