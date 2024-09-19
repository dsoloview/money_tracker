<?php

namespace App\Observers;

use Elastic\Elasticsearch\Client;
use Illuminate\Database\Eloquent\Model;

class ElasticSearchObserver
{
    public function __construct(private Client $client)
    {
    }

    public function created(Model $model)
    {
        $model->elasticsearchIndex($this->client);
    }

    public function updated(Model $model)
    {
        $model->elasticsearchIndex($this->client);
    }

    public function deleted(Model $model)
    {
        $model->elasticsearchDelete($this->client);
    }
}
