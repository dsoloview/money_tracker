<?php

namespace App\Telegram\Command;

use App\Services\Account\AccountService;
use App\Services\Telegram\TelegramUserService;
use Telegram\Bot\Commands\Command;

class AccountsCommand extends Command
{
    protected string $name = 'accounts';

    protected string $description = 'Get info about your accounts';

    protected AccountService $accountService;
    protected TelegramUserService $telegramUserService;

    public function __construct(AccountService $accountService, TelegramUserService $telegramUserService)
    {
        $this->accountService = $accountService;
        $this->telegramUserService = $telegramUserService;
    }

    public function handle()
    {
        $telegramUser = $this->telegramUserService->getTelegramUserByTelegramId(
            $this->getUpdate()->getMessage()->getFrom()->getId()
        );

        $user = $telegramUser->user;
        $accounts = $this->accountService->getUserAccounts($user);
        $totalBalance = $accounts->sum('user_currency_balance');


        if ($accounts->isEmpty()) {
            $this->replyWithMessage([
                'text' => 'You have no accounts yet.',
            ]);
            return;
        }


        $this->replyWithMessage([
            'text' => view('telegram.accounts',
                compact('accounts', 'user', 'totalBalance'))->render(),
            'parse_mode' => 'HTML',
        ]);
    }
}
