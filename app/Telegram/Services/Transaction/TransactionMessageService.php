<?php

namespace App\Telegram\Services\Transaction;

use App\Models\Telegram\TelegramUser;
use App\Services\User\Transaction\UserTransactionService;
use App\Telegram\Services\TelegramKeyboardService;
use Telegram\Bot\Laravel\Facades\Telegram;

class TransactionMessageService
{
    private UserTransactionService $userTransactionService;

    public function __construct(UserTransactionService $userTransactionService)
    {
        $this->userTransactionService = $userTransactionService;
    }

    public function sendTransactionsMessage(TelegramUser $telegramUser)
    {
        $user = $telegramUser->user;
        $transactions = $this->userTransactionService->getUserTransactionsPaginated($user);

        if ($transactions->isEmpty()) {
            Telegram::sendMessage([
                'chat_id' => $telegramUser->chat_id,
                'text' => 'You have no transactions yet',
            ]);

            return;
        }

        $currentPage = $transactions->currentPage();
        $totalPages = $transactions->lastPage();


        Telegram::sendMessage([
            'chat_id' => $telegramUser->chat_id,
            'text' => view('telegram.transactions',
                compact('transactions', 'user'))->render(),
            'parse_mode' => 'HTML',
            'reply_markup' => TelegramKeyboardService::getTransactionsPaginationKeyboard($currentPage, $totalPages),
        ]);
    }
}
