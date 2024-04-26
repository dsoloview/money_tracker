<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Telegram\TelegramUserResource;
use App\Models\Telegram\TelegramUser;
use App\Models\User;
use App\Services\Telegram\TelegramTokenService;
use App\Services\Telegram\TelegramUserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'User Telegram', description: 'User Telegram')]
#[Authenticated]
class UserTelegramController extends Controller
{
    public function __construct(
        private readonly TelegramTokenService $telegramTokenService,
        private readonly TelegramUserService $telegramUserService
    ) {
    }

    #[Endpoint('Get Telegram user for user')]
    #[ResponseFromApiResource(TelegramUserResource::class, TelegramUser::class)]
    public function getTelegramUser(User $user): TelegramUserResource
    {
        $this->authorize('view', $user);
        $telegramUser = $this->telegramUserService->getUsersTelegramUser($user);

        if (!$telegramUser) {
            throw new ModelNotFoundException();
        }

        return new TelegramUserResource($telegramUser);
    }

    #[Endpoint('Get Telegram token for user')]
    #[Response([
        'data' => [
            'token' => 'string',
        ],
    ])]
    public function token(User $user): JsonResponse
    {
        $this->authorize('view', $user);
        $token = $this->telegramTokenService->generateTokenForUser($user);

        return response()->json([
            'data' => [
                'token' => $token,
            ],
        ]);
    }

    #[Endpoint('Logout from Telegram')]
    #[Response([
        'success' => 'bool',
    ])]
    public function logout(User $user): JsonResponse
    {
        $this->authorize('view', $user);
        $this->telegramUserService->logoutByUser($user);

        return response()->json([
            'success' => true
        ]);
    }
}
