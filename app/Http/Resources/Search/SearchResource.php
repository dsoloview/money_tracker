<?php

namespace App\Http\Resources\Search;

use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Transaction\TransactionResource;
use App\Http\Resources\Transfer\TransferResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SearchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $categories = $this->resource->categories->map(fn($category) => new CategoryResource($category));
        $transactions = $this->resource->transactions->map(fn($transaction) => new TransactionResource($transaction));
        $transfers = $this->resource->transfers->map(fn($transfer) => new TransferResource($transfer));

        return [
            'categories' => $categories,
            'transactions' => $transactions,
            'transfers' => $transfers,
        ];
    }
}
