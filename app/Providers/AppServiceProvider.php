<?php

namespace App\Providers;

use App\Api\ExchangeRate\ExchangeRateApi;
use App\Interfaces\IExchangeRateFetcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(IExchangeRateFetcher::class, ExchangeRateApi::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(! $this->app->isProduction());
    }
}
