<?php

namespace App\Data\Transaction;

use App\Enums\Category\CategoryTransactionTypes;
use Spatie\LaravelData\Data;

class TransactionData extends Data
{
    public function __construct(
        public ?string $comment,
        public float $amount,
        public array $categories_ids,
        public CategoryTransactionTypes $type,
    ) {
    }
}
