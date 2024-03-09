<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\Transaction\Transaction */
class TransactionCollection extends ResourceCollection
{
    private int $minAmount;
    private int $maxAmount;

    public function __construct($resource, int $minAmount, int $maxAmount)
    {
        parent::__construct($resource);
        $this->minAmount = $minAmount;
        $this->maxAmount = $maxAmount;
    }


    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'min_amount' => $this->minAmount,
            'max_amount' => $this->maxAmount,
        ];
    }
}
