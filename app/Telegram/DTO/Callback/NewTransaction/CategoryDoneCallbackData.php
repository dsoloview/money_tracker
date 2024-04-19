<?php

namespace App\Telegram\DTO\Callback\NewTransaction;

use App\Telegram\DTO\Callback\ICallbackData;

class CategoryDoneCallbackData implements ICallbackData
{
    public int $transactionId;

    public static function fromArray(array $data): ICallbackData
    {
        $model = new self();

        $model->transactionId = (int) $data[0];

        return $model;
    }

    public function toArray(): array
    {
        return [
            $this->transactionId,
        ];
    }
}
