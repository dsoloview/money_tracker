<?php

namespace App\Data\Transaction;

use App\Enums\Category\CategoryTransactionTypes;
use Spatie\LaravelData\Data;

class TransactionData extends Data
{
    public function __construct(
        public int $account_id,
        public ?string $comment,
        public int $amount,
        public array $categories_ids,
        public CategoryTransactionTypes $type,
    ) {
    }
}
