<?php

namespace Tests\Unit\Services\Telegram;

use App\Models\Telegram\TelegramUser;
use App\Models\User;
use App\Notifications\Telegram\TelegramLogoutNotification;
use App\Services\Telegram\TelegramUserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TelegramUserServiceTest extends TestCase
{
    use RefreshDatabase;

    private TelegramUserService $telegramUserService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->telegramUserService = app(TelegramUserService::class);
    }

    public function testGetUsersTelegramUser()
    {
        $user = User::factory()->create();
        $telegramUser = TelegramUser::factory()->create(['user_id' => $user->id]);

        $fetchedTelegramUser = $this->telegramUserService->getUsersTelegramUser($user);

        $this->assertEquals($telegramUser->id, $fetchedTelegramUser->id);
    }

    public function testGetTelegramUserByTelegramId()
    {
        $telegramUser = TelegramUser::factory()->create(['telegram_id' => 123456]);

        $fetchedTelegramUser = $this->telegramUserService->getTelegramUserByTelegramId(123456);

        $this->assertEquals($telegramUser->id, $fetchedTelegramUser->id);
    }

    public function testGetTelegramUserByChatId()
    {
        $telegramUser = TelegramUser::factory()->create(['chat_id' => 654321]);

        $fetchedTelegramUser = $this->telegramUserService->getTelegramUserByChatId(654321);

        $this->assertEquals($telegramUser->id, $fetchedTelegramUser->id);
    }

    public function testUpdateOrCreateTelegramUserCreatesNew()
    {
        $telegramUser = $this->telegramUserService->updateOrCreateTelegramUser(789012, 345678, 'username');

        $this->assertDatabaseHas('telegram_users', [
            'telegram_id' => 789012,
            'chat_id' => 345678,
            'username' => 'username',
        ]);

        $this->assertEquals(789012, $telegramUser->telegram_id);
    }

    public function testUpdateOrCreateTelegramUserUpdatesExisting()
    {
        $telegramUser = TelegramUser::factory()->create([
            'telegram_id' => 789012,
            'chat_id' => 111111,
            'username' => 'old_username',
        ]);

        $updatedTelegramUser = $this->telegramUserService->updateOrCreateTelegramUser(789012, 345678, 'new_username');

        $this->assertDatabaseHas('telegram_users', [
            'telegram_id' => 789012,
            'chat_id' => 345678,
            'username' => 'new_username',
        ]);

        $this->assertEquals($telegramUser->id, $updatedTelegramUser->id);
    }

    public function testAuthorizeTelegramUser()
    {
        $user = User::factory()->create();
        $telegramUser = TelegramUser::factory()->create();

        $this->telegramUserService->authorize($telegramUser, $user);

        $this->assertDatabaseHas(TelegramUser::class, [
            'id' => $telegramUser->id,
            'user_id' => $user->id,
        ]);
    }

    public function testLogoutByUser()
    {
        Notification::fake();

        $user = User::factory()->create();
        $telegramUser = TelegramUser::factory()->create(['user_id' => $user->id]);

        $this->telegramUserService->logoutByUser($user);

        $this->assertDatabaseHas('telegram_users', [
            'id' => $telegramUser->id,
            'user_id' => null,
        ]);

        Notification::assertSentTo($telegramUser, TelegramLogoutNotification::class);
    }

    public function testLogoutByUserWithoutTelegramUserThrowsException()
    {
        $user = User::factory()->create();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('User does not have Telegram user.');

        $this->telegramUserService->logoutByUser($user);
    }

    public function testLogoutTelegramUser()
    {
        $user = User::factory()->create();
        $telegramUser = TelegramUser::factory()->create(['user_id' => $user->id]);

        $this->telegramUserService->logout($telegramUser);

        $this->assertDatabaseHas(TelegramUser::class, [
            'id' => $telegramUser->id,
            'user_id' => null,
        ]);
    }

    public function testLogoutByTelegramId()
    {
        $user = User::factory()->create();
        $telegramUser = TelegramUser::factory()->create(['telegram_id' => 123456, 'user_id' => $user->id]);

        $this->telegramUserService->logoutByTelegramId(123456);
        
        $this->assertDatabaseHas('telegram_users', [
            'telegram_id' => 123456,
            'user_id' => null,
        ]);
    }
}
