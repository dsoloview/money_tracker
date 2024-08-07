<?php

namespace App\Services\Telegram;

use App\Models\Telegram\TelegramUser;
use App\Models\User;
use App\Notifications\Telegram\TelegramLogoutNotification;

class TelegramUserService
{
    public function getUsersTelegramUser(User $user): ?TelegramUser
    {
        return $user->telegramUser;
    }

    public function getTelegramUserByTelegramId(int $telegramId): ?TelegramUser
    {
        return TelegramUser::with([
            'state', 'user', 'user.settings', 'user.settings.mainCurrency',
        ])->where('telegram_id', $telegramId)->first();
    }

    public function getTelegramUserByChatId(int $chatId): ?TelegramUser
    {
        return TelegramUser::where('chat_id', $chatId)->first();
    }

    public function updateOrCreateTelegramUser(int $telegramId, int $chatId, string $username): TelegramUser
    {
        return TelegramUser::updateOrCreate([
            'telegram_id' => $telegramId,
        ], [
            'telegram_id' => $telegramId,
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

    public function logoutByUser(User $user): void
    {
        if (!$user->isAuthorizedInTelegram()) {
            throw new \Exception('User does not have Telegram user.');
        }

        $telegramUser = $user->telegramUser;
        $telegramUser->notify(new TelegramLogoutNotification());

        $this->logout($telegramUser);
    }

    public function logout(TelegramUser $telegramUser): void
    {
        $telegramUser->update([
            'user_id' => null,
        ]);
    }

    public function logoutByTelegramId(int $telegramId): void
    {
        TelegramUser::where('telegram_id', $telegramId)->update([
            'user_id' => null,
        ]);
    }
}
