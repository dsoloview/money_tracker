<?php

namespace App\Telegram\Controller\Message;

use App\Services\Currency\CurrencyService;
use App\Services\Telegram\TelegramUserStateService;
use App\Telegram\Enum\State\Step\TelegramNewAccountStateStep;
use App\Telegram\Enum\State\TelegramState;
use App\Telegram\Facades\TgUser;
use App\Telegram\Intrerface\ITelegramController;
use App\Telegram\Processor\TelegramNewAccountCache;
use App\Telegram\Services\Account\AccountMessageService;
use Telegram\Bot\Objects\Update;

readonly class NewAccountController implements ITelegramController
{
    public function __construct(
        private AccountMessageService $accountMessageService,
        private TelegramUserStateService $telegramUserStateService,
        private CurrencyService $currencyService
    ) {
    }

    public function process(Update $update): void
    {
        $step = TgUser::state()?->data['step'];

        if ($step === null) {
            throw new \Exception('Step is not defined');
        }

        $this->{$step}($update);
    }

    public function name(Update $update): void
    {
        $account['name'] = $update->getMessage()->getText();

        TelegramNewAccountCache::putAccountToCache($account);

        $this->telegramUserStateService->updateOrCreateStateByTelegramId(
            $update->getMessage()->getFrom()->getId(),
            TelegramState::NEW_ACCOUNT,
            [
                'step' => TelegramNewAccountStateStep::BANK->value
            ]
        );

        $this->accountMessageService->sendAccountBankMessage();
    }

    public function bank(Update $update)
    {
        $account = TelegramNewAccountCache::getAccountFromCache();

        $account['bank'] = $update->getMessage()->getText();

        TelegramNewAccountCache::putAccountToCache($account);

        $this->telegramUserStateService->updateOrCreateStateByTelegramId(
            $update->getMessage()->getFrom()->getId(),
            TelegramState::NEW_ACCOUNT,
            [
                'step' => TelegramNewAccountStateStep::BALANCE->value
            ]
        );

        $this->accountMessageService->sendAccountBalanceMessage();
    }

    public function balance(Update $update)
    {
        $account = TelegramNewAccountCache::getAccountFromCache();

        $account['balance'] = $update->getMessage()->getText();

        TelegramNewAccountCache::putAccountToCache($account);

        $this->telegramUserStateService->resetState(TgUser::get());

        $this->accountMessageService->sendAccountCurrencyMessage(
            $this->currencyService->index()
        );
    }

}
