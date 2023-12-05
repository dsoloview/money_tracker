<?php

namespace App\Data\User;

use Spatie\LaravelData\Attributes\Validation\Confirmed;
use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;

class UserCreateData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    )
    {
    }
}
