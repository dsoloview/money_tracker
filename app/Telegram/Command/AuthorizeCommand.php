<?php

namespace App\Telegram\Command;

use App\Models\Telegram\TelegramUserState;
use App\Telegram\Enum\TelegramAuthStateStep;
use App\Telegram\Enum\TelegramState;
use Telegram\Bot\Commands\Command;

class AuthorizeCommand extends Command
{
    protected string $name = 'authorize';

    protected string $description = 'Authorize Command';

    public function handle()
    {
        TelegramUserState::updateOrCreate([
            'telegram_user_id' => $this->getUpdate()->getMessage()->getFrom()->getId(),
        ], [
            'state' => TelegramState::AUTH,
            'data' => [
                'step' => TelegramAuthStateStep::EMAIL,
            ],
        ]);

        $this->replyWithMessage([
            'text' => 'Send me your email address to authorize.',
        ]);
    }
}
