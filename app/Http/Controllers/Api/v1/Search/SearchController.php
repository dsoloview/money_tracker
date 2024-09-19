<?php

namespace App\Http\Controllers\Api\v1\Search;

use App\Http\Controllers\Controller;
use App\Http\Requests\Search\SearchRequest;
use App\Http\Resources\Search\SearchCollection;
use App\Http\Resources\Search\SearchResource;
use App\Interfaces\Search\ISearchService;

class SearchController extends Controller
{
    public function __construct(
        private ISearchService $searchService
    ) {
    }

    public function search(SearchRequest $request)
    {
        $result = $this->searchService->search('t');

        return response()->json([
            'data' => (new SearchResource($result))->toArray($request)
        ]);
    }
}
