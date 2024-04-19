<?php

namespace App\Telegram\DTO\Callback\NewTransaction;

use App\Enums\Category\CategoryTransactionType;

class TransactionTypeCallbackData
{
    public CategoryTransactionType $type;
    public int $transactionId;

    public static function fromArray(array $data): self
    {
        $model = new self();

        $model->type = CategoryTransactionType::fromCode($data[0]);
        $model->transactionId = $data[1];

        return $model;
    }

    public function toArray(): array
    {
        return [
            $this->type->getCode(),
            $this->transactionId,
        ];
    }
}
