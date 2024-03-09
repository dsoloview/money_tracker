<?php

namespace App\Data\Account;

use Spatie\LaravelData\Data;

class AccountData extends Data
{
    public function __construct(
        public int $currency_id,
        public float $balance,
        public string $name,
        public string $bank,
    ) {
    }
}
