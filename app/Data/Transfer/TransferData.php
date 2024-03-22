<?php

namespace App\Data\Transfer;

use Spatie\LaravelData\Data;

class TransferData extends Data
{
    public function __construct(
        public int $account_to_id,
        public ?string $comment,
        public float $amount_from,
        public float $amount_to,
        public string $date,
    ) {
    }
}
