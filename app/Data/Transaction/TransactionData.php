<?php

namespace App\Data\Transaction;

use Spatie\LaravelData\Data;

class TransactionData extends Data
{
    public function __construct(
        public int $account_id,
        public ?string $comment,
        public int $amount,
    )
    {
    }
}
