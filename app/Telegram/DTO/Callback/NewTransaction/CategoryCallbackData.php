<?php

namespace App\Telegram\DTO\Callback\NewTransaction;

use App\Telegram\DTO\Callback\ICallbackData;

class CategoryCallbackData implements ICallbackData
{
    public int $categoryId;
    public int $transactionId;

    public static function fromArray(array $data): self
    {
        $model = new self();

        $model->categoryId = (int) $data[0];
        $model->transactionId = (int) $data[1];

        return $model;
    }

    public function toArray(): array
    {
        return [
            $this->transactionId,
            $this->categoryId,
        ];
    }

    public function isSelected(): bool
    {
        return $this->categoryId < 0;
    }
}
