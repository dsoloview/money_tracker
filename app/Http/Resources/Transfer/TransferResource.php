<?php

namespace App\Http\Resources\Transfer;

use App\Http\Resources\Account\AccountResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Transfer\Transfer */
class TransferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'comment' => $this->comment,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'account_from' => new AccountResource($this->whenLoaded('accountFrom')),
            'account_to' => new AccountResource($this->whenLoaded('accountTo')),
        ];
    }
}
