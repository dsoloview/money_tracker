<?php

namespace App\Data\User\Newsletter;

use App\Enums\Newsletter\NewsletterPeriodsEnum;
use Spatie\LaravelData\Data;

class UserNewsletterUpdateData extends Data
{
    public function __construct(
        public NewsletterPeriodsEnum $period,
    ) {
    }
}
