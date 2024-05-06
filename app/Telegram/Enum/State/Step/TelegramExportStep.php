<?php

namespace App\Telegram\Enum\State\Step;

enum TelegramExportStep: string
{
    case DATE_FROM = 'dateFrom';
    case DATE_TO = 'dateTo';
    case CONFIRMATION = 'confirmation';
}
