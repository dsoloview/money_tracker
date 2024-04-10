<?php

namespace App\Services\Telegram;

use App\Models\Telegram\TelegramUser;
use App\Telegram\Enum\TelegramState;

class TelegramUserStateService
{
    public function updateState(TelegramUser $telegramUser, TelegramState $state, array $data = []): void
    {
        $telegramUser->state->update([
            'state' => $state->value,
            'data' => $data,
        ]);
    }

    public function resetState(TelegramUser $telegramUser): void
    {
        $telegramUser->state->update([
            'state' => null,
            'data' => null,
        ]);
    }
}
