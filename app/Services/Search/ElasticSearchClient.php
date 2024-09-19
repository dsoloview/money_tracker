<?php

namespace App\Services\Search;

use Elastic\Elasticsearch\Client;

class ElasticSearchClient
{

    public function __construct(private Client $client)
    {
    }

    public function search(array $params): array
    {
        $response = $this->client->search($params);

        dd($response->asArray());
        return $response['hits']['hits'];
    }
}
