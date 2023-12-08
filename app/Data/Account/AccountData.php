<?php

namespace App\Data\Account;

use Spatie\LaravelData\Data;

class AccountData extends Data
{
    public function __construct(
        public int $user_id,
        public int $currency_id,
        public int $balance,
        public string $name,
        public string $bank,
    )
    {
    }
}
