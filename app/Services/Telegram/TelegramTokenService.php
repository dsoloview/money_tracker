<?php

namespace App\Services\Telegram;

use App\Models\Telegram\TelegramToken;
use App\Models\User;

class TelegramTokenService
{
    public function generateTokenForUser(User $user): string
    {
        $token = bin2hex(random_bytes(5));

        TelegramToken::updateOrCreate(
            ['user_id' => $user->id],
            ['token' => $token]
        );

        return $token;
    }
}
