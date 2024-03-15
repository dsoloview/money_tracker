<?php

namespace App\Http\Resources\Account;

use App\Http\Resources\Currency\CurrencyResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Account\Account */
class AccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'bank' => $this->bank,
            'balance' => $this->balance,
            'user_currency_balance' => $this->userCurrencyBalance,
            'user' => new UserResource($this->whenLoaded('user')),
            'currency' => new CurrencyResource($this->whenLoaded('currency')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
