<?php

namespace App\Repositories\Transaction;

use App\Models\Transaction\Transaction;

class TransactionRepository
{
    public function getFilteredTransactionsInfoForUser(int $userId): array
    {
        return Transaction::filterBy('$eq')
            ->filter()
            ->leftJoin('accounts', 'transactions.account_id', 'accounts.id')
            ->leftJoin('users', 'accounts.user_id', 'users.id')
            ->leftJoin('currencies', 'accounts.currency_id', 'currencies.id')
            ->whereRaw('users.id = ?', [$userId])
            ->selectRaw('
                    currencies.code as currency,
                     SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) / 100 as total_expense,
                    SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) / 100 as total_income,
                    min(amount) / 100 as min_transaction,
                    max(amount) / 100 as max_transaction
                    ')
            ->groupBy(['currencies.id'])
            ->get(['total_expense', 'total_income', 'min_transaction', 'max_transaction'])
            ->toArray();
    }
}
