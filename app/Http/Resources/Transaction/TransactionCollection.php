<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/** @see \App\Models\Transaction\Transaction */
class TransactionCollection extends ResourceCollection
{

    /**
     * @param  string  $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
    }


    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
        ];
    }
}
