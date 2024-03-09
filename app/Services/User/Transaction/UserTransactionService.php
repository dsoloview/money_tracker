<?php

namespace App\Services\User\Transaction;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserTransactionService
{
    public function getUserTransactionsPaginated(User $user): LengthAwarePaginator
    {
        return $user
            ->transactions()
            ->filter()
            ->sort()
            ->with('account', 'account.currency', 'categories')
            ->paginate(10);
    }

    public function getMinTransactionAmount(User $user): int
    {
        $minAmount =  $user
            ->transactions()
            ->min('amount');

        return $minAmount / 100;
    }

    public function getMaxTransactionAmount(User $user): int
    {
        $maxAmount = $user
            ->transactions()
            ->max('amount');

        return $maxAmount / 100;
    }

}
