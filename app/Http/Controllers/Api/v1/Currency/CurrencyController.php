<?php

namespace App\Http\Controllers\Api\v1\Currency;

use App\Http\Controllers\Controller;
use App\Http\Resources\Currency\CurrencyCollection;
use App\Http\Resources\Currency\CurrencyResource;
use App\Models\Currency\Currency;
use App\Services\Currency\CurrencyService;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group('Currency')]
#[Authenticated]
class CurrencyController extends Controller
{
    public function __construct(
        private readonly CurrencyService $currencyService
    ) {
    }

    #[Endpoint('List of currencies')]
    #[ResponseFromApiResource(CurrencyCollection::class, Currency::class)]
    public function index(): CurrencyCollection
    {
        return new CurrencyCollection($this->currencyService->index());
    }

    #[Endpoint('Show currency')]
    #[ResponseFromApiResource(CurrencyResource::class, Currency::class)]
    public function show(Currency $currency): CurrencyResource
    {
        return new CurrencyResource($currency);
    }
}
