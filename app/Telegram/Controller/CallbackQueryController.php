<?php

namespace App\Telegram\Controller;

use App\Telegram\DTO\CallbackQuery;
use App\Telegram\Intrerface\ITelegramController;
use Telegram\Bot\Objects\Update;

class CallbackQueryController implements ITelegramController
{
    public function process(Update $update): void
    {
        $data = $update->getCallbackQuery()->getData();

        $callbackQuery = CallbackQuery::fromJson($data);

        $controller = $callbackQuery->group->getCallbackController();
        $controller->process($update);
    }
}
