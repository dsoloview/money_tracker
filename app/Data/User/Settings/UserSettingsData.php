<?php

namespace App\Data\User\Settings;

use Spatie\LaravelData\Data;

class UserSettingsData extends Data
{
    public function __construct(
        public int $main_currency_id,
        public int $language_id,
    ) {
    }
}
