<?php

namespace App\Telegram\Command;

use App\Services\Telegram\TelegramUserStateService;
use App\Telegram\Enum\State\Step\TelegramImportStep;
use App\Telegram\Enum\State\TelegramState;
use App\Telegram\Services\TelegramKeyboardService;
use Telegram\Bot\Commands\Command;

class ImportCommand extends Command
{
    protected string $name = 'import';

    protected string $description = 'Import data from zen money';

    public function __construct(
        private readonly TelegramUserStateService $telegramUserStateService,
    ) {
    }

    public function handle()
    {
        $this->telegramUserStateService->updateOrCreateStateByTelegramId(
            $this->getUpdate()->getMessage()->getFrom()->getId(),
            TelegramState::IMPORT,
            [
                'step' => TelegramImportStep::IMPORT_MODE->value
            ]
        );

        $this->replyWithMessage([
            'text' => 'Please select import mode: 1 if you want to create categories/accounts automatically, 2 if you want to import only categories/accounts you already have.',
            'reply_markup' => TelegramKeyboardService::getImportModeKeyboard()
        ]);
    }
}
