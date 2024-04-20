<?php

namespace App\Telegram\Controller\Callback;

use App\Telegram\DTO\CallbackQuery;
use App\Telegram\Intrerface\ITelegramController;
use Telegram\Bot\Objects\Update;

abstract class AbstractCallbackController implements ITelegramController
{
    protected const array AVAILABLE_TYPES = [];

    public function process(Update $update): void
    {
        $callbackQuery = CallbackQuery::fromJson($update->getCallbackQuery()->getData());

        $group = $callbackQuery->group;

        $methodName = $group->getCallbackType($callbackQuery->type)->getMethodName();
        if (! in_array($methodName, static::AVAILABLE_TYPES)) {
            return;
        }

        $this->{$methodName}($update, $callbackQuery);
    }
}
