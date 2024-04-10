<?php

namespace App\Policies\Transfer;

use App\Models\Account\Account;
use App\Models\Transfer\Transfer;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TransferPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user, Account $account): bool
    {
        return $user->id === $account->user_id;
    }

    public function view(User $user, Transfer $transfer): bool
    {
        return $user->id === $transfer->accountTo->user_id;
    }

    public function create(User $user, Account $account, int $accountToId): bool
    {
        $accountTo = Account::findOrFail($accountToId);

        return $user->id === $account->user_id && $user->id === $accountTo->user_id;
    }

    public function update(User $user, Transfer $transfer, int $accountToId): bool
    {
        return $user->id === $transfer->accountTo->user_id && $user->id === $accountToId;
    }

    public function delete(User $user, Transfer $transfer): bool
    {
        return $user->id === $transfer->accountTo->user_id;
    }

    public function restore(User $user, Transfer $transfer): bool
    {
        return $user->id === $transfer->accountTo->user_id;
    }

    public function forceDelete(User $user, Transfer $transfer): bool
    {
        return $user->id === $transfer->accountTo->user_id;
    }
}
