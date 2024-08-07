<?php

namespace App\Data\Transfer;

use Spatie\LaravelData\Data;

class TransferUpdateData extends Data
{
    public function __construct(
        public int $account_from_id,
        public int $account_to_id,
        public ?string $comment,
        public int $amount_from,
        public int $amount_to,
    ) {
    }
}
