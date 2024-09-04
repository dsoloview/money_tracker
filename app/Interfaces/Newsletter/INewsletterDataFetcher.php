<?php

namespace App\Interfaces\Newsletter;

use App\Enums\Newsletter\NewsletterPeriodsEnum;
use Illuminate\Support\Collection;

interface INewsletterDataFetcher
{
    /**
     * @param  NewsletterPeriodsEnum  $period
     * @param  Collection  $users
     * @return array
     */
    public function fetch(NewsletterPeriodsEnum $period, Collection $users): array;
}
