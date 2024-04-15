<?php

namespace App\Http\Controllers;

use App\Telegram\Controller\TelegramController;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramWebhookController extends Controller
{
    public function __invoke(string $token, TelegramController $telegramController)
    {
        if ($token !== config('telegram.bots.mybot.token')) {
            abort(403);
        }

        $update = Telegram::getWebhookUpdates();

        $telegramController->process($update);
    }
}
