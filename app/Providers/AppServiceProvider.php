<?php

namespace App\Providers;

use App\Api\ExchangeRate\Implementations\ExchangeRateApi\ExchangeRateApi;
use App\Api\ExchangeRate\Implementations\ExchangeRateApi\ResponseMapper;
use App\Api\ExchangeRate\Interfaces\IExchangeRateFetcher;
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
