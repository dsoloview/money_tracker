<?php

namespace App\Enums\Newsletter;

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
}
