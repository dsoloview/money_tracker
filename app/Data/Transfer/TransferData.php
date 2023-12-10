<?php

namespace App\Data\Transfer;

use Spatie\LaravelData\Data;

class TransferData extends Data
{
    public function __construct(
        public int $account_to_id,
        public ?string $comment,
        public int $amount,
    )
    {
    }
}
