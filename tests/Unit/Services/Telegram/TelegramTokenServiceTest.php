<?php

namespace Tests\Unit\Services\Telegram;

use App\Models\Telegram\TelegramToken;
use App\Models\User;
use App\Services\Telegram\TelegramTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TelegramTokenServiceTest extends TestCase
{
    use RefreshDatabase;

    private TelegramTokenService $telegramTokenService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->telegramTokenService = app(TelegramTokenService::class);
    }

    public function testGenerateTokenForUserCreatesAndStoresToken()
    {
        $user = User::factory()->create();

        $token = $this->telegramTokenService->generateTokenForUser($user);

        $this->assertIsString($token);
        $this->assertDatabaseHas(TelegramToken::class, [
            'user_id' => $user->id,
        ]);

        $telegramToken = TelegramToken::where('user_id', $user->id)->first();

        $this->assertNotNull($telegramToken);
        $this->assertTrue(Hash::check($token, $telegramToken->token));
    }

    public function testGenerateTokenForUserUpdatesExistingToken()
    {
        $user = User::factory()->create();

        $oldToken = $this->telegramTokenService->generateTokenForUser($user);

        $this->assertDatabaseHas(TelegramToken::class, [
            'user_id' => $user->id,
        ]);
        $oldTokenFromDb = TelegramToken::where('user_id', $user->id)->first();

        $this->assertNotNull($oldTokenFromDb);
        $this->assertTrue(Hash::check($oldToken, $oldTokenFromDb->token));


        $newToken = $this->telegramTokenService->generateTokenForUser($user);

        $this->assertNotEquals($oldToken, $newToken);

        $newTokenFromDB = TelegramToken::where('user_id', $user->id)->first();

        $this->assertNotNull($newTokenFromDB);
        $this->assertTrue(Hash::check($newToken, $newTokenFromDB->token));
    }
}
