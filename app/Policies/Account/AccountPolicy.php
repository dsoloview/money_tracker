<?php

namespace App\Policies\Account;

use App\Models\Account\Account;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    public function view(User $user, Account $account): bool
    {
        return $user->id === $account->user_id;
    }

    public function create(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    public function update(User $user, Account $account): bool
    {
        return $user->id === $account->user_id;
    }

    public function delete(User $user, Account $account): bool
    {
        return $user->id === $account->user_id;
    }

    public function restore(User $user, Account $account): bool
    {
        return $user->id === $account->user_id;
    }

    public function forceDelete(User $user, Account $account): bool
    {
        return $user->id === $account->user_id;
    }
}
