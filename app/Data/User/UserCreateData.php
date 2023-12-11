<?php

namespace App\Data\User;

use App\Data\User\Setting\UserSettingData;
use Spatie\LaravelData\Data;

class UserCreateData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public UserSettingData $settings,
    ) {
    }
}
