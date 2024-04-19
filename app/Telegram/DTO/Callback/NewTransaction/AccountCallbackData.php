<?php

namespace App\Telegram\DTO\Callback\NewTransaction;

use App\Telegram\DTO\Callback\ICallbackData;

class AccountCallbackData implements ICallbackData
{
    public int $accountId;

    public static function fromArray(array $data): self
    {
        $model = new self();

        $model->accountId = (int) $data[0];

        return $model;
    }

    public function toArray(): array
    {
        return [
            $this->accountId,
        ];
    }
}
