<?php

namespace App\Data\User;

use App\Data\User\Settings\UserSettingsData;
use Spatie\LaravelData\Data;

class UserCreateData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public UserSettingsData $settings,
    ) {
    }
}
