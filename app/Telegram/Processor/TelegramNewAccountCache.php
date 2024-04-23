<?php

namespace App\Telegram\Processor;

use App\Services\Telegram\TelegramUserStateService;
use App\Telegram\Exception\TelegramVisibleException;
use App\Telegram\Facades\TgUser;

class TelegramNewAccountCache
{
    public static function getAccountFromCache(): array
    {
        $account = \Cache::get('tg_user_new_account_'.TgUser::telegramId());

        if (!$account) {
            $telegramUserStateService = app(TelegramUserStateService::class);
            $telegramUserStateService->resetState(TgUser::get());
            throw new TelegramVisibleException("Please start the account creation process again");
        }

        return $account;
    }

    public static function putAccountToCache(array $account): void
    {
        \Cache::put('tg_user_new_account_'.TgUser::telegramId(), $account, now()->addMinutes(5));
    }

    public static function forgetAccountFromCache(): void
    {
        \Cache::forget('tg_user_new_account_'.TgUser::telegramId());
    }
}
