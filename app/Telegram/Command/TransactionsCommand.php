<?php

namespace App\Telegram\Command;

use App\Services\Telegram\TelegramUserService;
use App\Services\User\Transaction\UserTransactionService;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class TransactionsCommand extends Command
{
    protected string $name = 'transactions';

    protected string $description = 'Get info about your transactions';

    protected UserTransactionService $userTransactionService;
    protected TelegramUserService $telegramUserService;

    public function __construct(
        UserTransactionService $userTransactionService,
        TelegramUserService $telegramUserService
    ) {
        $this->userTransactionService = $userTransactionService;
        $this->telegramUserService = $telegramUserService;
    }

    public function handle()
    {
        $telegramUser = $this->telegramUserService->getTelegramUserByTelegramId(
            $this->getUpdate()->getMessage()->getFrom()->getId()
        );

        $user = $telegramUser->user;
        $transactions = $this->userTransactionService->getUserTransactionsPaginated($user);


        if ($transactions->isEmpty()) {
            $this->replyWithMessage([
                'text' => 'You have no transactions yet.',
            ]);
            return;
        }

        $currentPage = $transactions->currentPage();


        $this->replyWithMessage([
            'text' => view('telegram.transactions',
                compact('transactions', 'user'))->render(),
            'parse_mode' => 'HTML',
            'reply_markup' => Keyboard::make()->inline()->row([
                Keyboard::inlineButton([
                    'text' => '🔙 Back',
                    'callback_data' => "transactions_page_".($currentPage - 1),
                ]),
                Keyboard::inlineButton([
                    'text' => '🔜 Next',
                    'callback_data' => "transactions_page_".($currentPage + 1),
                ]),
            ])->setOneTimeKeyboard(true)
        ]);
    }
}
