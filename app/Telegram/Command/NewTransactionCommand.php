<?php

namespace App\Telegram\Command;

use App\Telegram\Facades\TgUser;
use App\Telegram\Services\Transaction\TransactionMessageService;
use Telegram\Bot\Commands\Command;

class NewTransactionCommand extends Command
{
    protected string $name = 'new_transaction';

    protected string $description = 'Create a new transaction';

    private TransactionMessageService $transactionMessageService;

    public function __construct(TransactionMessageService $transactionMessageService)
    {
        $this->transactionMessageService = $transactionMessageService;
    }

    public function handle()
    {
        $this->transactionMessageService->sendTransactionAccountsMessage(TgUser::get());
    }
}
