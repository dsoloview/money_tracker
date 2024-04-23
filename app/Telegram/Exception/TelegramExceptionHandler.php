<?php

namespace App\Telegram\Exception;

use App\Telegram\Facades\TgUser;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramExceptionHandler
{
    public function handle(\Throwable $exception)
    {
        report($exception);
        if ($exception instanceof TelegramVisibleException) {
            Telegram::sendMessage([
                'chat_id' => TgUser::chatId(),
                'text' => $exception->getMessage(),
            ]);

            return;
        }

        if (app()->environment('local')) {
            Telegram::sendMessage([
                'chat_id' => TgUser::chatId(),
                'text' => $exception->getMessage(),
            ]);
        } else {
            Telegram::sendMessage([
                'chat_id' => TgUser::chatId(),
                'text' => 'Something went wrong. Please try again later. If the problem persists, please use /reset command.',
            ]);
        }
    }
}
