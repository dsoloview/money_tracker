<?php

namespace App\Http\Controllers\api\v1\Currency;

use App\Http\Controllers\Controller;
use App\Http\Resources\Currency\CurrencyCollection;
use App\Http\Resources\Currency\CurrencyResource;
use App\Models\Currency\Currency;
use App\Services\Currency\CurrencyService;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function __construct(
        private readonly CurrencyService $currencyService
    )
    {
    }

    public function index(): CurrencyCollection
    {
        return new CurrencyCollection($this->currencyService->index());
    }

    public function show(Currency $currency): CurrencyResource
    {
        return new CurrencyResource($currency);
    }
}
