<?php

namespace App\Telegram\DTO\Callback\NewAccount;

use App\Telegram\DTO\Callback\ICallbackData;

class CurrencyCallbackData implements ICallbackData
{
    public int $currencyId;

    public static function fromArray(array $data): ICallbackData
    {
        $self = new self();

        $self->currencyId = (int) $data[0];

        return $self;
    }

    public function toArray(): array
    {
        return [
            $this->currencyId,
        ];
    }
}
