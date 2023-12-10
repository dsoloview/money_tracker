<?php

namespace App\Services\Transaction;

use App\Data\Transaction\TransactionData;
use App\Enums\Category\CategoryTransactionTypes;
use App\Models\Account\Account;
use App\Models\Transaction\Transaction;
use App\Services\Account\AccountService;
use Illuminate\Support\Collection;

class TransactionService
{
    public function __construct(
        private readonly AccountService $accountService
    ) {
    }

    public function getAccountTransactions(Account $account): Collection
    {
        return $account->transactions->load('categories');
    }

    public function getAccountTransactionsPaginated(Account $account): Collection
    {
        return $account->transactions()->paginate(10)->load('categories');
    }

    public function createTransactionForAccount(Account $account, TransactionData $data): Transaction
    {
        return \DB::transaction(function () use ($account, $data) {
            $transaction = $account->transactions()->create($data->all());
            $transaction->categories()->sync($data->categories_ids);

            if ($transaction->type === CategoryTransactionTypes::INCOME) {
                $this->accountService->increaseAccountBalance($account, $transaction->amount);
            } else {
                $this->accountService->decreaseAccountBalance($account, $transaction->amount);
            }

            return $transaction->load('categories');
        });
    }

    public function updateTransaction(Transaction $transaction, TransactionData $data): Transaction
    {
        return \DB::transaction(function () use ($transaction, $data) {
            $transaction->update($data->all());
            $transaction->categories()->sync($data->categories_ids);

            if ($transaction->type === CategoryTransactionTypes::INCOME) {
                $this->accountService->decreaseAccountBalance($transaction->account, $transaction->amount);
            } else {
                $this->accountService->increaseAccountBalance($transaction->account, $transaction->amount);
            }

            return $transaction;
        });
    }

    public function deleteTransaction(Transaction $transaction): void
    {
        \DB::transaction(function () use ($transaction) {
            if ($transaction->type === CategoryTransactionTypes::INCOME) {
                $this->accountService->decreaseAccountBalance($transaction->account, $transaction->amount);
            } else {
                $this->accountService->increaseAccountBalance($transaction->account, $transaction->amount);
            }

            $transaction->delete();
        });
    }
}
