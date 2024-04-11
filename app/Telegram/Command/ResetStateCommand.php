<?php

namespace App\Telegram\Command;

use App\Services\Telegram\TelegramUserService;
use App\Services\Telegram\TelegramUserStateService;
use Telegram\Bot\Commands\Command;

class ResetStateCommand extends Command
{
    protected string $name = 'reset';

    protected string $description = 'Reset Command to reset the state of the user';

    protected TelegramUserService $telegramUserService;
    protected TelegramUserStateService $telegramUserStateService;

    public function __construct(
        TelegramUserService $telegramUserService,
        TelegramUserStateService $telegramUserStateService
    ) {
        $this->telegramUserService = $telegramUserService;
        $this->telegramUserStateService = $telegramUserStateService;
    }

    public function handle()
    {
        $telegramId = $this->getUpdate()->getMessage()->getFrom()->getId();
        $telegramUser = $this->telegramUserService->getTelegramUserByTelegramId($telegramId);
        $this->telegramUserStateService->resetState($telegramUser);

        $this->replyWithMessage([
            'text' => 'State has been reset',
        ]);
    }
}
