<?php

namespace App\Interfaces\Search;

use App\Data\Search\SearchData;

interface ISearchService
{
    public function search(string $query): SearchData;
}
