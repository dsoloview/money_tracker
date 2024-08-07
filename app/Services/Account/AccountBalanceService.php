<?php

namespace App\Services\Account;

use App\Data\Transaction\TransactionData;
use App\Enums\Category\CategoryTransactionType;
use App\Models\Account\Account;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Services\Currency\CurrencyConverterService;

class AccountBalanceService
{
    public function __construct(
        private CurrencyConverterService $currencyConverterService
    ) {
    }

    public function getUserAccountsBalance(User $user): float
    {
        $accounts = $user->accounts;
        $totalBalance = 0;

        foreach ($accounts as $account) {
            $totalBalance += $this->getAccountBalanceInMainCurrency($account, $user);
        }

        return round($totalBalance, 2);
    }

    public function getAccountBalanceInMainCurrency(Account $account, User $user): float
    {
        $accountCurrency = $account->currency->code;
        $accountBalance = $account->balance;
        $userMainCurrency = $user->currency->code;

        if ($userMainCurrency !== $accountCurrency) {
            $accountBalance = $this->currencyConverterService->convert(
                $accountBalance,
                $accountCurrency,
                $userMainCurrency
            );
        }

        return $accountBalance;
    }

    public function updateAccountBalanceWhenTransactionUpdated(
        Account $account,
        Transaction $transaction,
        TransactionData $transactionData
    ): Account {
        $oldTransactionAmount = $transaction->amount;
        $newTransactionAmount = $transactionData->amount;

        $oldTransactionType = $transaction->type;
        $newTransactionType = $transactionData->type;

        $balance = $account->balance;

        if ($oldTransactionType === CategoryTransactionType::INCOME) {
            $balance -= $oldTransactionAmount;
        } else {
            $balance += $oldTransactionAmount;
        }

        if ($newTransactionType === CategoryTransactionType::INCOME) {
            $balance += $newTransactionAmount;
        } else {
            $balance -= $newTransactionAmount;
        }

        return $this->updateAccountBalance($account, $balance);
    }

    private function updateAccountBalance(
        Account $account,
        float $newBalance
    ): Account {
        $account->balance = $newBalance;
        $account->save();

        return $account;
    }

    public function increaseAccountBalance(Account $account, float $amount): Account
    {
        $account->balance += $amount;
        $account->save();

        return $account;
    }

    public function decreaseAccountBalance(Account $account, float $amount): Account
    {
        $account->balance -= $amount;
        $account->save();

        return $account;
    }
}
