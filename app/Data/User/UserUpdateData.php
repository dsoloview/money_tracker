<?php

namespace App\Data\User;

use Spatie\LaravelData\Data;

class UserUpdateData extends Data
{
    public function __construct(
        public string $email,
        public string $name,
        public ?string $password
    ) {
    }

    public function all(): array
    {
        $data = [
            'email' => $this->email,
            'name' => $this->name,
        ];

        if ($this->password) {
            $data['password'] = $this->password;
        }

        return $data;
    }
}
