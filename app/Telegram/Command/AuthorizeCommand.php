<?php

namespace App\Telegram\Command;

use App\Services\Telegram\TelegramUserStateService;
use App\Telegram\Enum\TelegramAuthStateStep;
use App\Telegram\Enum\TelegramState;
use Telegram\Bot\Commands\Command;

class AuthorizeCommand extends Command
{
    protected string $name = 'authorize';

    protected string $description = 'Authorize Command';

    protected TelegramUserStateService $telegramUserStateService;

    public function __construct(TelegramUserStateService $telegramUserStateService)
    {
        $this->telegramUserStateService = $telegramUserStateService;
    }

    public function handle()
    {
        $this->telegramUserStateService->updateOrCreateStateByTelegramId(
            $this->getUpdate()->getMessage()->getFrom()->getId(),
            TelegramState::AUTH,
            [
                'step' => TelegramAuthStateStep::EMAIL,
            ]
        );

        $this->replyWithMessage([
            'text' => 'Send me your email address to authorize.',
        ]);
    }
}
