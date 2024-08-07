<?php

namespace App\Providers;

use App\Api\ExchangeRate\Implementations\ExchangeRateApi\ExchangeRateApi;
use App\Api\ExchangeRate\Implementations\ExchangeRateApi\ResponseMapper;
use App\Api\ExchangeRate\Interfaces\IExchangeRateFetcher;
use Illuminate\Database\Eloquent\Model;
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
    }
}
