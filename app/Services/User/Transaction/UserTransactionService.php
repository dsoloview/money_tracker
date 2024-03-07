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
}
