<?php

namespace App\Enums\Newsletter;

use Carbon\Carbon;

enum NewsletterPeriodsEnum: string
{
    case OFF = 'OFF';
    case DAILY = 'DAILY';
    case WEEKLY = 'WEEKLY';
    case MONTHLY = 'MONTHLY';

    public static function keys(): array
    {
        return [
            self::OFF->name,
            self::DAILY->name,
            self::WEEKLY->name,
            self::MONTHLY->name,
        ];
    }

    public static function toArrayWithTranslations(): array
    {
        return [
            self::OFF->name => __('enums.newsletter_periods_enum.'.self::OFF->name),
            self::DAILY->name => __('enums.newsletter_periods_enum.'.self::DAILY->name),
            self::WEEKLY->name => __('enums.newsletter_periods_enum.'.self::WEEKLY->name),
            self::MONTHLY->name => __('enums.newsletter_periods_enum.'.self::MONTHLY->name),
        ];
    }

    public function getDateFrom(): Carbon
    {
        return match ($this) {
            self::DAILY => Carbon::now()->startOfDay()->subDay(),
            self::WEEKLY => Carbon::now()->startOfDay()->subWeek(),
            self::MONTHLY => Carbon::now()->startOfDay()->subMonth(),
            default => throw new \Exception('Not implemented yet'),
        };
    }

    public function getDateTo(): Carbon
    {
        return match ($this) {
            self::DAILY, self::WEEKLY, self::MONTHLY => Carbon::now(),
            default => throw new \Exception('Not implemented yet'),
        };
    }
}
