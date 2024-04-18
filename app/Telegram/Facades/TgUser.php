<?php

namespace App\Telegram\Facades;

use App\Models\Telegram\TelegramUser;
use App\Models\Telegram\TelegramUserState;
use App\Models\User;
use App\Services\Telegram\TelegramUserService;

class TgUser
{
    private static TelegramUser $user;

    public static function setUser(int $telegramId): TelegramUser
    {
        $telegramUserService = app(TelegramUserService::class);
        $user = $telegramUserService->getTelegramUserByTelegramId($telegramId);
        self::$user = $user;

        return $user;
    }

    public static function updateOrCreateTelegramUser(int $userId, int $chatId, string $username): TelegramUser
    {
        $telegramUserService = app(TelegramUserService::class);
        $user = $telegramUserService->updateOrCreateTelegramUser($userId, $chatId, $username);
        self::$user = $user;

        return $user;
    }

    public static function get(): TelegramUser
    {
        return self::$user;
    }

    public static function user(): ?User
    {
        return self::$user->user;
    }

    public static function state(): ?TelegramUserState
    {
        return self::$user->state;
    }

    public static function hasState(): bool
    {
        return self::$user->state?->state !== null;
    }

    public static function isAuthorized(): bool
    {
        return self::$user->user_id !== null;
    }

    public static function chatId(): int
    {
        return self::$user->chat_id;
    }

    public static function telegramId(): int
    {
        return self::$user->telegram_id;
    }
}
