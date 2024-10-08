<?php

namespace App\Services\User\Transaction;

use App\Data\Transaction\TransactionsInfoData;
use App\Models\User;
use App\Repositories\Transaction\TransactionRepository;
use App\Services\Currency\CurrencyConverterService;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserTransactionService
{
    public function getUserTransactionsPaginated(User $user, ?int $page = null): LengthAwarePaginator
    {
        return $user
            ->transactions()
            ->filter()
            ->sort()
            ->with('account', 'account.currency', 'categories')
            ->paginate(perPage: request()->get('per_page') ?? 10, page: $page);
    }

    public function getUserTransactionsFilteredByDates(User $user, Carbon $fromDate, Carbon $toDate): Collection
    {
        return $user
            ->transactions()
            ->whereDate('date', '>=', $fromDate)
            ->whereDate('date', '<=', $toDate)
            ->with('account', 'account.currency', 'categories')
            ->get();
    }

    public function getMinTransactionAmount(User $user): int
    {
        $minAmount = $user
            ->transactions()
            ->filterBy('$eq')
            ->filter()
            ->min('amount');

        return $minAmount / 100;
    }

    public function getMaxTransactionAmount(User $user): int
    {
        $maxAmount = $user
            ->transactions()
            ->filterBy('$eq')
            ->filter()
            ->max('amount');

        return $maxAmount / 100;
    }

    public function getTransactionsInfoForUser(User $user): TransactionsInfoData
    {
        $repository = app(TransactionRepository::class);

        $data = $repository->getFilteredTransactionsInfoForUser($user->id);

        $result = new TransactionsInfoData(
            currency: $user->currency,
            total_expense: 0,
            total_income: 0,
            min_transaction: PHP_INT_MAX,
            max_transaction: 0,
        );

        $currencyConverterService = app(CurrencyConverterService::class);
        foreach ($data as $value) {
            $result->increaseTotalExpense($currencyConverterService->convertToUserCurrency($value['total_expense'],
                $value['currency'], $user));
            $result->increaseTotalIncome($currencyConverterService->convertToUserCurrency($value['total_income'],
                $value['currency'], $user));
            $result->updateMinTransaction($currencyConverterService->convertToUserCurrency($value['min_transaction'],
                $value['currency'], $user));
            $result->updateMaxTransaction($currencyConverterService->convertToUserCurrency($value['max_transaction'],
                $value['currency'], $user));
        }

        return $result;
    }
}
