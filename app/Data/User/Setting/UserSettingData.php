<?php

namespace App\Data\User\Setting;

use Spatie\LaravelData\Data;

class UserSettingData extends Data
{
    public function __construct(
        public int $main_currency_id,
        public int $language_id,
    ) {
    }
}
