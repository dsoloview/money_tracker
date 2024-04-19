<?php

namespace App\Telegram\Command;

use App\Services\Telegram\TelegramUserStateService;
use App\Telegram\Facades\TgUser;
use Telegram\Bot\Commands\Command;

class ResetStateCommand extends Command
{
    protected string $name = 'reset';

    protected string $description = 'Reset Command to reset the state of the user';
    protected TelegramUserStateService $telegramUserStateService;

    public function __construct(
        TelegramUserStateService $telegramUserStateService
    ) {
        $this->telegramUserStateService = $telegramUserStateService;
    }

    public function handle()
    {
        $this->telegramUserStateService->resetState(TgUser::get());

        $this->replyWithMessage([
            'text' => 'State has been reset',
        ]);
    }
}
