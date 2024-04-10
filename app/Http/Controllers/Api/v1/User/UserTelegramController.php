<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Telegram\TelegramTokenService;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;

#[Group(name: 'User Telegram', description: 'User Telegram')]
#[Authenticated]
class UserTelegramController extends Controller
{
    public function __construct(
        private readonly TelegramTokenService $telegramTokenService
    ) {
    }

    #[Endpoint('Get Telegram token for user')]
    #[Response([
        'data' => [
            'token' => 'string',
        ],
    ])]
    public function token(User $user)
    {
        $this->authorize('view', $user);
        $token = $this->telegramTokenService->generateTokenForUser($user);

        return response()->json([
            'data' => [
                'token' => $token,
            ],
        ]);
    }
}
