<?php

namespace App\Telegram\Controller\Callback;

use App\Models\Telegram\TelegramUser;
use App\Telegram\DTO\CallbackQuery;
use App\Telegram\Intrerface\ITelegramController;
use Telegram\Bot\Objects\Update;

abstract class AbstractCallbackController implements ITelegramController
{
    protected const AVAILABLE_TYPES = [];

    public function process(Update $update, TelegramUser $telegramUser): void
    {
        $callbackQuery = CallbackQuery::fromJson($update->getCallbackQuery()->getData());

        if (!in_array($callbackQuery->type, static::AVAILABLE_TYPES)) {
            return;
        }

        $this->{$callbackQuery->type}($update, $telegramUser, $callbackQuery);
    }
}
