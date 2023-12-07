<?php

namespace App\Http\Resources\Currency;

use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserSetting\UserSettingResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Currency\Currency */
class CurrencyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'user_settings' => UserSettingResource::collection($this->whenLoaded('userSettings')),
            'users' => UserResource::collection($this->whenLoaded('user')),
        ];
    }
}
