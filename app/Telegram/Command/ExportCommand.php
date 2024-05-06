<?php

namespace App\Telegram\Command;

use App\Services\Telegram\TelegramUserStateService;
use App\Telegram\Enum\State\Step\TelegramExportStep;
use App\Telegram\Enum\State\TelegramState;
use Telegram\Bot\Commands\Command;

class ExportCommand extends Command
{
    protected string $name = 'export';

    protected string $description = 'Export data to Excel file.';

    public function __construct(
        private readonly TelegramUserStateService $telegramUserStateService,
    ) {
    }

    public function handle()
    {
        $this->telegramUserStateService->updateOrCreateStateByTelegramId(
            $this->getUpdate()->getMessage()->getFrom()->getId(),
            TelegramState::EXPORT,
            [
                'step' => TelegramExportStep::DATE_FROM->value
            ]
        );

        $this->replyWithMessage([
            'text' => 'Please enter the start date in the format YYYY-MM-DD',
        ]);
    }
}
