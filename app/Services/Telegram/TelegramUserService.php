<?php

namespace App\Services\Telegram;

use App\Models\Telegram\TelegramUser;
use App\Models\User;

class TelegramUserService
{
    public function getTelegramUserByTelegramId(int $telegramId): ?TelegramUser
    {
        return TelegramUser::where('telegram_id', $telegramId)->first();
    }

    public function getTelegramUserByChatId(int $chatId): ?TelegramUser
    {
        return TelegramUser::where('chat_id', $chatId)->first();
    }

    public function updateOrCreateTelegramUser(int $userId, int $chatId, string $username): TelegramUser
    {
        return TelegramUser::updateOrCreate([
            'telegram_id' => $userId,
        ], [
            'telegram_id' => $userId,
            'chat_id' => $chatId,
            'username' => $username,
        ]);
    }

    public function authorize(TelegramUser $telegramUser, User $user): void
    {
        $telegramUser->update([
            'user_id' => $user->id,
        ]);
    }
}
