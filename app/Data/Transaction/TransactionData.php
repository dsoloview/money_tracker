<?php

namespace App\Data\Transaction;

use App\Enums\Category\CategoryTransactionType;
use Spatie\LaravelData\Data;

class TransactionData extends Data
{
    public function __construct(
        public ?string $comment,
        public float $amount,
        public array $categories_ids,
        public CategoryTransactionType $type,
    ) {
    }
}
