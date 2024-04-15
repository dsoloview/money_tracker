<?php

namespace App\Services\Telegram;

use App\Models\Telegram\TelegramUser;
use App\Models\Telegram\TelegramUserState;
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

    public function updateOrCreateStateByTelegramId(int $telegramId, TelegramState $state, array $data = []): void
    {
        TelegramUserState::updateOrCreate([
            'telegram_user_id' => $telegramId,
        ], [
            'state' => $state,
            'data' => $data,
        ]);
    }

    public function resetState(TelegramUser $telegramUser): void
    {
        $telegramUser->state->updateOrCreate([
            'state' => null,
            'data' => null,
        ]);
    }
}
