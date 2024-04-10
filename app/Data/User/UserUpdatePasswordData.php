<?php

namespace App\Data\User;

use Spatie\LaravelData\Data;

class UserUpdatePasswordData extends Data
{
    public function __construct(
        public string $current_password,
        public string $password,
        public string $password_confirmation,

    ) {
    }
}
