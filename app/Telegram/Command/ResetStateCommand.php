<?php

namespace App\Telegram\Command;

use App\Services\Telegram\TelegramUserService;
use App\Services\Telegram\TelegramUserStateService;
use Telegram\Bot\Commands\Command;

class ResetStateCommand extends Command
{
    protected string $name = 'reset';

    protected string $description = 'Reset Command to reset the state of the user';

    public function handle()
    {
        $telegramId = $this->getUpdate()->getMessage()->getFrom()->getId();
        $telegramUser = app(TelegramUserService::class)->getTelegramUserByTelegramId($telegramId);
        app(TelegramUserStateService::class)->resetState($telegramUser);

        $this->replyWithMessage([
            'text' => 'State has been reset',
        ]);
    }
}
