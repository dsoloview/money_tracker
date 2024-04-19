<?php

namespace App\Telegram\DTO\Callback;

interface ICallbackData
{
    public static function fromArray(array $data): self;

    public function toArray(): array;
}
