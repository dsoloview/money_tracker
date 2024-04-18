<?php

namespace App\Telegram\Command;

use App\Services\Telegram\TelegramUserService;
use App\Telegram\Facades\TgUser;
use Telegram\Bot\Commands\Command;

class LogoutCommand extends Command
{
    protected string $name = 'logout';

    protected string $description = 'Command to logout from the system.';

    private TelegramUserService $telegramUserService;

    public function __construct(TelegramUserService $telegramUserService)
    {
        $this->telegramUserService = $telegramUserService;
    }

    public function handle()
    {
        $this->telegramUserService->logoutByTelegramId(
            TgUser::telegramId()
        );

        $this->replyWithMessage([
            'text' => 'You have been successfully logged out.',
        ]);
    }
}
