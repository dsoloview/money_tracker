<?php

namespace App\Services\Search;

use App\Data\Search\SearchData;
use App\Interfaces\Search\ISearchService;
use App\Models\Category\Category;
use App\Models\Transaction\Transaction;
use App\Models\Transfer\Transfer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ScoutSearchService implements ISearchService
{
    private int $userId;

    public function __construct()
    {
        $this->userId = \Auth::id();
    }

    public function search(string $query): SearchData
    {
        $categories = $this->getCategories($query);
        $transactions = $this->getTransactions($query);
        $transfers = $this->getTransfers($query);

        return new SearchData($categories, $transactions, $transfers);
    }

    private function getCategories(string $query): Collection
    {
        return Category::search($query)->where('user_id', $this->userId)->take(3)->get();
    }

    private function getTransactions(string $query): Collection
    {
        return Transaction::search($query)->query(function (Builder $builder) {
            $builder->join('accounts', 'transactions.account_id', '=', 'accounts.id')
                ->join('currencies', 'accounts.currency_id', '=', 'currencies.id')
                ->where('accounts.user_id', $this->userId)
                ->select('transactions.*')
                ->orderBy('transactions.date', 'desc')
                ->with(['account', 'account.currency', 'account.user', 'account.user.currency']);
        })->take(3)->get();
    }

    private function getTransfers(string $query): Collection
    {
        return Transfer::search($query)->query(function (Builder $builder) {
            $builder->join('accounts', 'transfers.account_from_id', '=', 'accounts.id')
                ->join('currencies', 'accounts.currency_id', '=', 'currencies.id')
                ->where('accounts.user_id', $this->userId)
                ->select('transfers.*')
                ->orderBy('transfers.date', 'desc')
                ->with(['account_from', 'account_to', 'account_from.currency', 'account_to.currency']);
        })->take(3)->get();
    }
}
