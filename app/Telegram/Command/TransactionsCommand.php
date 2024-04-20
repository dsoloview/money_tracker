<?php

namespace App\Telegram\Command;

use App\Telegram\Facades\TgUser;
use App\Telegram\Services\Transaction\TransactionMessageService;
use Telegram\Bot\Commands\Command;

class TransactionsCommand extends Command
{
    protected string $name = 'transactions';

    protected string $description = 'Get info about your transactions';

    protected TransactionMessageService $transactionMessageService;

    public function __construct(
        TransactionMessageService $transactionMessageService
    ) {
        $this->transactionMessageService = $transactionMessageService;
    }

    public function handle()
    {
        $this->transactionMessageService->sendTransactionsMessage(TgUser::get());
    }
}
