<?php

namespace App\Services\Transaction;

use App\Enums\Category\CategoryTransactionType;
use App\Models\Transaction\Transaction;
use Carbon\Carbon;

class TelegramTransactionService
{
    public function __construct(
        private TransactionService $transactionService
    ) {
    }

    public function createTelegramTransaction(int $accountId): Transaction
    {
        return Transaction::create([
            'account_id' => $accountId,
            'amount' => 0,
            'comment' => 'Telegram transaction',
            'isFinished' => false,
            'type' => CategoryTransactionType::EXPENSE,
        ]);
    }

    public function setTransactionType(int $transactionId, CategoryTransactionType $type): Transaction
    {
        $transaction = $this->transactionService->getTransactionById($transactionId);
        $transaction->type = $type;
        $transaction->save();

        return $transaction;
    }

    public function addTransactionCategory(int $transactionId, int $categoryId): Transaction
    {
        $transaction = $this->transactionService->getTransactionById($transactionId);
        $transaction->categories()->attach($categoryId);

        return $transaction;
    }

    public function removeTransactionCategory(int $transactionId, int $categoryId): Transaction
    {
        $transaction = $this->transactionService->getTransactionById($transactionId);
        $transaction->categories()->detach($categoryId);

        return $transaction;
    }

    public function setTransactionAmount(int $transactionId, int $amount): Transaction
    {
        $transaction = $this->transactionService->getTransactionById($transactionId);
        $transaction->amount = $amount;
        $transaction->save();

        return $transaction;
    }

    public function setTransactionComment(int $transactionId, string $comment): Transaction
    {
        $transaction = $this->transactionService->getTransactionById($transactionId);
        $transaction->comment = $comment;
        $transaction->save();

        return $transaction;
    }

    public function setTransactionDate(int $transactionId, Carbon $date): Transaction
    {
        $transaction = $this->transactionService->getTransactionById($transactionId);
        $transaction->date = $date;
        $transaction->save();

        return $transaction;
    }

    public function finishTransaction(int $transactionId): Transaction
    {
        $transaction = $this->transactionService->getTransactionById($transactionId);
        $transaction->isFinished = true;
        $transaction->save();

        return $transaction;
    }
}
