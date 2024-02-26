<?php

namespace App\Data\User;

use App\Data\User\Setting\UserSettingData;
use Spatie\LaravelData\Data;

class UserUpdateData extends Data
{
    public function __construct(
        public string $email,
        public string $name,
        public UserSettingData $settings,
    ) {
    }
}
