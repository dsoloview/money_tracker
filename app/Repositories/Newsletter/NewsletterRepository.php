<?php

namespace App\Repositories\Newsletter;

use App\Models\User;
use Carbon\Carbon;

class NewsletterRepository
{
    public function getStatisticsData(Carbon $dateFrom, Carbon $dateTo, array $usersIds): array
    {
        return User::whereIn('users.id', $usersIds)
            ->whereDate('transactions.date', '>=', $dateFrom)
            ->whereDate('transactions.date', '<=', $dateTo)
            ->leftJoin('accounts', 'users.id', 'accounts.user_id')
            ->leftJoin('transactions', 'accounts.id', 'transactions.account_id')
            ->leftJoin('currencies', 'accounts.currency_id', 'currencies.id')
            ->selectRaw('
                users.id as user_id,
                currencies.code as currency,
                SUM(CASE WHEN transactions.type = "income" THEN transactions.amount ELSE 0 END) / 100 as total_income,
                SUM(CASE WHEN transactions.type = "expense" THEN transactions.amount ELSE 0 END) / 100 as total_expense,
                COUNT(transactions.id) as transactions_count,
                SUM(accounts.balance) / 100 as total_balance
                ')
            ->groupBy(['user_id', 'currencies.id'])
            ->get(['user_id', 'currency', 'total_expense', 'total_income', 'total_transactions', 'total_balance'])
            ->toArray();
    }
}
