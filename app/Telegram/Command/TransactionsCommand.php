<?php

namespace App\Telegram\Command;

use App\Services\Telegram\TelegramUserService;
use App\Services\User\Transaction\UserTransactionService;
use App\Telegram\Services\Transaction\TransactionMessageService;
use Telegram\Bot\Commands\Command;

class TransactionsCommand extends Command
{
    protected string $name = 'transactions';

    protected string $description = 'Get info about your transactions';

    protected UserTransactionService $userTransactionService;

    protected TelegramUserService $telegramUserService;
    protected TransactionMessageService $transactionMessageService;

    public function __construct(
        UserTransactionService $userTransactionService,
        TelegramUserService $telegramUserService,
        TransactionMessageService $transactionMessageService
    ) {
        $this->userTransactionService = $userTransactionService;
        $this->telegramUserService = $telegramUserService;
        $this->transactionMessageService = $transactionMessageService;
    }

    public function handle()
    {
        $telegramUser = $this->telegramUserService->getTelegramUserByTelegramId(
            $this->getUpdate()->getMessage()->getFrom()->getId()
        );

        $this->transactionMessageService->sendTransactionsMessage($telegramUser);
    }
}
