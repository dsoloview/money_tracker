<?php

namespace App\Services\Telegram;

use App\Models\Telegram\TelegramUser;
use App\Models\Telegram\TelegramUserState;
use App\Telegram\Enum\State\TelegramState;

class TelegramUserStateService
{
    public function updateState(TelegramUser $telegramUser, TelegramState $state, array $data = []): void
    {
        $telegramUser->state->update([
            'state' => $state->value,
            'data' => $data,
        ]);
    }

    public function updateOrCreateStateByTelegramId(
        int $telegramId,
        ?TelegramState $state,
        ?array $data = []
    ): void {
        $telegramUser = TelegramUser::where('telegram_id', $telegramId)->firstOrFail();

        TelegramUserState::updateOrCreate([
            'telegram_user_id' => $telegramUser->id,
        ], [
            'state' => $state,
            'data' => $data,
        ]);
    }

    public function updateOrCreateStateByTelegramUserId(
        int $telegramUserId,
        ?TelegramState $state,
        ?array $data = []
    ) {
        TelegramUserState::updateOrCreate([
            'telegram_user_id' => $telegramUserId,
        ], [
            'state' => $state,
            'data' => $data,
        ]);
    }

    public function resetState(TelegramUser $telegramUser): void
    {
        $this->updateOrCreateStateByTelegramUserId($telegramUser->id, null, null);
    }
}
