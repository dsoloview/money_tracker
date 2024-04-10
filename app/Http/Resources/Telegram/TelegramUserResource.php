<?php

namespace App\Http\Resources\Telegram;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Telegram\TelegramUser */
class TelegramUserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'id' => $this->id,
            'telegram_id' => $this->telegram_id,
            'username' => $this->username,
            'user_id' => $this->user_id,
        ];
    }
}
