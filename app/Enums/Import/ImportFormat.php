<?php

namespace App\Enums\Import;

use App\Imports\MoneyTrackerImport;
use App\Imports\ZenMoneyImport;

enum ImportFormat: string
{
    case MONEY_TRACKER = 'MoneyTracker';
    case ZEN_MONEY = 'ZenMoney';

    public static function validateImportFormat(string $importFormat): bool
    {
        return in_array($importFormat, [self::MONEY_TRACKER->value, self::ZEN_MONEY->value]);
    }

    public function validateFileExtension(string $fileExtension): bool
    {
        return match ($this) {
            self::ZEN_MONEY => $fileExtension === 'csv',
            self::MONEY_TRACKER => $fileExtension === 'xlsx',
            default => false,
        };
    }

    public function getImportClass(): string
    {
        return match ($this) {
            self::ZEN_MONEY => ZenMoneyImport::class,
            self::MONEY_TRACKER => MoneyTrackerImport::class,
            default => throw new \Exception('Import format not found'),
        };
    }
}
