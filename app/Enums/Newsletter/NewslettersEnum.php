<?php

namespace App\Enums\Newsletter;

use App\Interfaces\Newsletter\INewsletterDataFetcher;
use App\Services\Newsletter\DataFetcher\StatisticsNewsletterDataFetcher;

enum NewslettersEnum: string
{
    case STATISTICS = 'statistics';
    case BALANCE = 'balance';

    public function getDataFetcher(): INewsletterDataFetcher
    {
        return match ($this) {
            self::STATISTICS => app(StatisticsNewsletterDataFetcher::class),
            self::BALANCE => throw new \Exception('Not implemented yet'),
        };
    }

}
