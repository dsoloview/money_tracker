<?php

namespace App\Services\Transaction;

use App\Data\Transaction\TransactionData;
use App\Enums\Category\CategoryTransactionType;
use App\Models\Account\Account;
use App\Models\Transaction\Transaction;
use App\Services\Account\AccountBalanceService;
use Illuminate\Support\Collection;

readonly class TransactionService
{
    public function __construct(
        private AccountBalanceService $accountBalanceService
    ) {
    }

    public function getAccountTransactions(Account $account): Collection
    {
        return $account->transactions->load('categories');
    }

    public function getTransactionById(int $transactionId): Transaction
    {
        return Transaction::withoutGlobalScopes()->findOrFail($transactionId);
    }

    public function getAccountTransactionsPaginated(Account $account): Collection
    {
        return $account->transactions()->paginate(10)->load('categories');
    }

    public function createTransactionForAccount(Account $account, TransactionData $data): Transaction
    {
        return \DB::transaction(function () use ($account, $data) {
            $saveData = $data->except('categories_ids');
            $transaction = $account->transactions()->create($saveData->all());
            $transaction->categories()->sync($data->categories_ids);

            $this->syncAccountBalanceForNewTransaction($transaction, $account);

            return $transaction->load('categories');
        });
    }

    public function syncAccountBalanceForNewTransaction(Transaction $transaction, Account $account): void
    {
        if ($transaction->type === CategoryTransactionType::INCOME) {
            $this->accountBalanceService->increaseAccountBalance($account, $transaction->amount);
        } else {
            $this->accountBalanceService->decreaseAccountBalance($account, $transaction->amount);
        }
    }

    public function updateTransaction(Transaction $transaction, TransactionData $data): Transaction
    {
        return \DB::transaction(function () use ($transaction, $data) {
            $this->accountBalanceService->updateAccountBalanceWhenTransactionUpdated(
                $transaction->account,
                $transaction,
                $data);

            $saveData = $data->except('categories_ids');
            $transaction->update($saveData->all());
            $transaction->categories()->sync($data->categories_ids);

            return $transaction;
        });
    }

    public function deleteTransaction(Transaction $transaction): void
    {
        \DB::transaction(function () use ($transaction) {
            if ($transaction->type === CategoryTransactionType::INCOME) {
                $this->accountBalanceService->decreaseAccountBalance($transaction->account, $transaction->amount);
            } else {
                $this->accountBalanceService->increaseAccountBalance($transaction->account, $transaction->amount);
            }

            $transaction->delete();
        });
    }
}
