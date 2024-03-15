<?php

namespace App\Services\Account;

use App\Data\Account\AccountData;
use App\Models\Account\Account;
use App\Models\User;
use Illuminate\Support\Collection;

class AccountService
{
    public function getUserAccounts(User $user): Collection
    {
        return $user->load('settings.mainCurrency')->accounts->load('currency');
    }

    public function saveAccountForUser(User $user, AccountData $data): Account
    {
        return $user->accounts()->create($data->all())->load('currency');
    }

    public function updateAccount(Account $account, AccountData $data): Account
    {
        $account->update($data->all());

        return $account->load('currency');
    }

    public function increaseAccountBalance(Account $account, float $amount): Account
    {
        $account->balance += $amount;
        $account->save();

        return $account->load('currency');
    }

    public function decreaseAccountBalance(Account $account, float $amount): Account
    {
        $account->balance -= $amount;
        $account->save();

        return $account->load('currency');
    }
}
