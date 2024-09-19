<?php

namespace App\Data\Search;

use Illuminate\Support\Collection;

class SearchData
{
    public Collection $categories;
    public Collection $transactions;
    public Collection $transfers;

    public function __construct(Collection $categories, Collection $transactions, Collection $transfers)
    {
        $this->categories = $categories;
        $this->transactions = $transactions;
        $this->transfers = $transfers;
    }
}
