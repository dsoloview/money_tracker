<?php

namespace App\Providers;

use App\Api\ExchangeRate\Implementations\ExchangeRateApi\ExchangeRateApi;
use App\Api\ExchangeRate\Implementations\ExchangeRateApi\ResponseMapper;
use App\Api\ExchangeRate\Interfaces\IExchangeRateFetcher;
use App\Interfaces\Search\ISearchRepository;
use App\Interfaces\Search\ISearchService;
use App\Services\Search\ElasticSearchService;
use App\Services\Search\ScoutSearchService;
use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(IExchangeRateFetcher::class, function () {
            return new ExchangeRateApi(new ResponseMapper);
        });

        $this->app->bind(ISearchService::class, function () {
            if (config('services.elasticsearch.enabled')) {
                return app(ElasticSearchService::class);
            }

            return app(ScoutSearchService::class);
        });

        $this->app->bind(Client::class, function () {
            return ClientBuilder::create()
                ->setHosts([
                    config('services.elasticsearch.connection.host').':'.config('services.elasticsearch.connection.port'),
                ])->build();
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(!$this->app->isProduction());

        Collection::macro('paginate', function ($perPage = 10) {
            $page = LengthAwarePaginator::resolveCurrentPage('page');

            return new LengthAwarePaginator($this->forPage($page, $perPage), $this->count(), $perPage, $page, [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => request()->query(),
            ]);
        });
    }
}
