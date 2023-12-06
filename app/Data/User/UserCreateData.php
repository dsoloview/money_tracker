<?php

namespace App\Data\User;

use Spatie\LaravelData\Data;

class UserCreateData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {
    }
}
