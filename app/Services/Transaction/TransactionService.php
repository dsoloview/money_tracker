<?php

namespace App\Services\Transaction;

use App\Data\Transaction\TransactionData;
use App\Http\Requests\Transaction\TransactionRequest;
use App\Models\Account\Account;
use App\Models\Transaction\Transaction;
use Illuminate\Support\Collection;

class TransactionService
{
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
        return $account->transactions()->create([
            'comment' => $data->comment,
            'amount' => $data->amount,
        ])->load('categories');
    }

    public function updateTransaction(Transaction $transaction, TransactionData $data): Transaction
    {
        $transaction->update($data->all());

        return $transaction->load('categories');
    }
}
