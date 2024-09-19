<?php

namespace App\Services\Search;

use App\Data\Search\SearchData;
use App\Interfaces\Search\ISearchService;

class ElasticSearchService implements ISearchService
{

    public function __construct(private ElasticSearchClient $client)
    {
    }

    public function search(string $query): SearchData
    {
        return new SearchData(collect(), collect(), collect());
    }
}
