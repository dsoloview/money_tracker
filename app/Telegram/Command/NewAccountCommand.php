<?php

namespace App\Telegram\Command;

use App\Services\Telegram\TelegramUserStateService;
use App\Telegram\Enum\State\Step\TelegramNewAccountStateStep;
use App\Telegram\Enum\State\TelegramState;
use App\Telegram\Processor\TelegramNewAccountCache;
use App\Telegram\Services\Account\AccountMessageService;
use Telegram\Bot\Commands\Command;

class NewAccountCommand extends Command
{
    protected string $name = 'new_account';

    protected string $description = 'Create a new account';


    public function __construct(
        private readonly AccountMessageService $accountMessageService,
        private readonly TelegramUserStateService $telegramUserStateService
    ) {
    }

    public function handle()
    {
        TelegramNewAccountCache::forgetAccountFromCache();

        $this->telegramUserStateService->updateOrCreateStateByTelegramId(
            $this->getUpdate()->getMessage()->getFrom()->getId(),
            TelegramState::NEW_ACCOUNT,
            [
                'step' => TelegramNewAccountStateStep::NAME->value
            ]
        );

        $this->accountMessageService->sendAccountNameMessage();
    }
}
