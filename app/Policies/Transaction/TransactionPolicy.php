<?php

namespace App\Policies\Transaction;

use App\Enums\Role\Roles;
use App\Models\Account\Account;
use App\Models\Transaction\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransactionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user, Account $account): bool
    {
        return $user->id === $account->user_id;
    }

    public function view(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->account->user_id;
    }

    public function create(User $user, Account $account): bool
    {
        return $user->id === $account->user_id;
    }

    public function update(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->account->user_id;
    }

    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->account->user_id;
    }

    public function restore(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->account->user_id;
    }

    public function forceDelete(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->account->user_id;
    }
}
