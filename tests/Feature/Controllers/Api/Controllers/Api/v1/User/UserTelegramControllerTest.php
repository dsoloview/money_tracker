<?php

namespace Tests\Feature\Controllers\Api\Controllers\Api\v1\User;

use App\Models\Telegram\TelegramUser;
use App\Notifications\Telegram\TelegramLogoutNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\WithTestUser;

class UserTelegramControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithTestUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTestUser();
    }

    public function testGetTelegramUser()
    {
        Sanctum::actingAs($this->user);
        TelegramUser::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson(route('users.telegram.user', ['user' => $this->user->id]));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'telegram_id',
                'chat_id',
                'username',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function testGetTelegramUserWithoutTelegramUser()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson(route('users.telegram.user', ['user' => $this->user->id]));

        $response->assertNotFound();
    }

    public function testGetAlienTelegramUser()
    {
        Sanctum::actingAs($this->user);
        $anotherUser = $this->createTestUser();

        $response = $this->getJson(route('users.telegram.user', ['user' => $anotherUser->id]));

        $response->assertForbidden();
    }

    public function testGetTelegramToken()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson(route('users.telegram.token', ['user' => $this->user->id]));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'token',
            ],
        ]);
    }

    public function testTelegramTokenGeneratesOnEachRequest()
    {
        Sanctum::actingAs($this->user);

        $response1 = $this->getJson(route('users.telegram.token', ['user' => $this->user->id]));
        $response2 = $this->getJson(route('users.telegram.token', ['user' => $this->user->id]));

        $response1->assertOk();
        $response2->assertOk();
        $response1->assertJsonStructure([
            'data' => [
                'token',
            ],
        ]);
        $response2->assertJsonStructure([
            'data' => [
                'token',
            ],
        ]);
        $this->assertNotEquals($response1->json('data.token'), $response2->json('data.token'));
    }

    public function testGetAlienTelegramToken()
    {
        Sanctum::actingAs($this->user);
        $anotherUser = $this->createTestUser();

        $response = $this->getJson(route('users.telegram.token', ['user' => $anotherUser->id]));

        $response->assertForbidden();
    }

    public function testLogout()
    {
        \Notification::fake();

        Sanctum::actingAs($this->user);

        $telegramUser = TelegramUser::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        $response = $this->postJson(route('users.telegram.logout', ['user' => $this->user->id]));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
        ]);

        $this->assertDatabaseHas(TelegramUser::class, [
            'user_id' => null,
        ]);

        \Notification::assertCount(1);
        \Notification::assertSentTo($telegramUser, TelegramLogoutNotification::class);
    }

    public function testLogoutWithoutTelegramUser()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson(route('users.telegram.logout', ['user' => $this->user->id]));

        $response->assertInternalServerError();
    }

    public function testLogoutAlienTelegramUser()
    {
        Sanctum::actingAs($this->user);
        $anotherUser = $this->createTestUser();

        $response = $this->postJson(route('users.telegram.logout', ['user' => $anotherUser->id]));

        $response->assertForbidden();
    }
}
