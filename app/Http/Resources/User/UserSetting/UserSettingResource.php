<?php

namespace App\Http\Resources\User\UserSetting;

use App\Http\Resources\Currency\CurrencyResource;
use App\Http\Resources\Language\LanguageResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\UserSetting */
class UserSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => new UserResource($this->whenLoaded('user')),
            'main_currency' => new CurrencyResource($this->whenLoaded('mainCurrency')),
            'language' => new LanguageResource($this->whenLoaded('language')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
