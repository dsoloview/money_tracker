<?php

namespace App\Traits;

use App\Observers\ElasticSearchObserver;
use Elastic\Elasticsearch\Client;

trait ElasticSearchable
{
    public static function bootElasticSearchable()
    {
        if (config('services.elasticsearch.enabled')) {
            static::observe(ElasticsearchObserver::class);
        }
    }

    public function elasticsearchIndex(Client $elasticsearchClient)
    {
        $elasticsearchClient->index([
            'index' => $this->getTable(),
            'type' => '_doc',
            'id' => $this->getKey(),
            'body' => $this->toElasticsearchDocumentArray(),
        ]);
    }

    public function elasticsearchDelete(Client $elasticsearchClient)
    {
        $elasticsearchClient->delete([
            'index' => $this->getTable(),
            'type' => '_doc',
            'id' => $this->getKey(),
        ]);
    }

    public static function indexAll()
    {
        $client = app(Client::class);

        (new static)->newQuery()->chunk(5000, function ($models) use ($client) {
            $params = [
                'index' => (new static)->getTable(),
                'body' => [],
            ];

            foreach ($models as $model) {
                $params['body'][] = [
                    'index' => [
                        '_id' => $model->getKey(),
                    ],
                ];

                $params['body'][] = $model->toElasticsearchDocumentArray();
            }

            $client->bulk($params);
        });
    }

    public static function clearIndex()
    {
        $client = app(Client::class);

        $client->indices()->delete([
            'index' => (new static)->getTable(),
        ]);
    }

    abstract public function toElasticSearchableArray(): array;
}
