<?php

namespace App\Http\Resources\Transaction;

use App\Http\Resources\Account\AccountResource;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Currency\CurrencyResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Transaction\Transaction */
class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'account_id' => $this->account_id,
            'comment' => $this->comment,
            'amount' => $this->amount,
            'user_currency_amount' => [
                'amount' => $this->user_currency_amount->getAmount(),
                'currency' => new CurrencyResource($this->user_currency_amount->getCurrency()),
            ],
            'account' => new AccountResource($this->whenLoaded('account')),
            'type' => $this->type,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),

            'date' => $this->date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
