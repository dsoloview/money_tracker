<?php

namespace App\Telegram\Enum\State\Step;

enum TelegramImportStep: string
{
    case IMPORT_MODE = 'importMode';
    case IMPORT_FORMAT = 'importFormat';
    case IMPORT_FILE = 'importFile';
}
