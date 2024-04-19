<?php

namespace App\Telegram\Services\Transaction;

use App\Models\Telegram\TelegramUser;
use App\Models\Transaction\Transaction;
use App\Services\Account\AccountService;
use App\Services\User\Transaction\UserTransactionService;
use App\Telegram\Facades\TgUser;
use App\Telegram\Services\TelegramKeyboardService;
use Illuminate\Support\Collection;
use Telegram\Bot\Laravel\Facades\Telegram;

readonly class TransactionMessageService
{
    public function __construct(
        private UserTransactionService $userTransactionService,
        private AccountService $accountService
    ) {
    }

    public function sendTransactionsMessage(TelegramUser $telegramUser): void
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

    public function sendCommentMessage(): void
    {
        Telegram::sendMessage([
            'chat_id' => TgUser::chatId(),
            'text' => 'Enter transaction comment',
        ]);
    }

    public function editCallbackMessage(int $messageId, string $text): void
    {
        Telegram::editMessageText([
            'chat_id' => TgUser::chatId(),
            'message_id' => $messageId,
            'text' => $text,
            'reply_markup' => null,
        ]);
    }

    public function sendDateMessage(): void
    {
        Telegram::sendMessage([
            'chat_id' => TgUser::chatId(),
            'text' => 'Enter transaction date',
        ]);
    }

    public function sendTransactionMessage(Transaction $transaction): void
    {
        $user = $transaction->account->user;

        Telegram::sendMessage([
            'chat_id' => $user->telegramUser->chat_id,
            'text' => view('telegram.transactionCreated',
                compact('transaction', 'user'))->render(),
            'parse_mode' => 'HTML',
        ]);
    }


    public function sendTransactionTypesMessage(TelegramUser $telegramUser, int $transactionId): void
    {
        Telegram::sendMessage([
            'chat_id' => $telegramUser->chat_id,
            'text' => 'Choose transaction type',
            'reply_markup' => TelegramKeyboardService::getTransactionTypesKeyboard($transactionId),
        ]);
    }

    public function sendTransactionAccountsMessage(TelegramUser $telegramUser): void
    {
        $user = $telegramUser->user;
        $accounts = $this->accountService->getUserAccounts($user);

        Telegram::sendMessage([
            'chat_id' => $telegramUser->chat_id,
            'text' => 'Choose account',
            'reply_markup' => TelegramKeyboardService::getAccountsKeyboard($accounts),
        ]);
    }

    public function sendTransactionCategoriesMessage(
        TelegramUser $telegramUser,
        Collection $categories,
        int $transactionId,
        int $messageId
    ): void {
        Telegram::editMessageText([
            'chat_id' => $telegramUser->chat_id,
            'message_id' => $messageId,
            'text' => 'Choose a category',
            'reply_markup' => TelegramKeyboardService::getCategoriesKeyboard($categories, $transactionId),
        ]);
    }

    public function sendTransactionAmountMessage(TelegramUser $telegramUser): void
    {
        Telegram::sendMessage([
            'chat_id' => $telegramUser->chat_id,
            'text' => 'Enter transaction amount',
        ]);
    }
}
