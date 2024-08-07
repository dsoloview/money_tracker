<?php

namespace Tests\Unit\Services\Telegram;

use App\Models\Telegram\TelegramUser;
use App\Models\Telegram\TelegramUserState;
use App\Services\Telegram\TelegramUserStateService;
use App\Telegram\Enum\State\TelegramState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TelegramUserStateServiceTest extends TestCase
{
    use RefreshDatabase;

    private TelegramUserStateService $telegramUserStateService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->telegramUserStateService = app(TelegramUserStateService::class);
    }

    public function testUpdateState()
    {
        $telegramUser = TelegramUser::factory()->create();
        $initialState = TelegramState::AUTH;
        $updatedState = TelegramState::NEW_TRANSACTION;

        $telegramUserState = TelegramUserState::factory()->create([
            'telegram_user_id' => $telegramUser->id,
            'state' => $initialState->value,
            'data' => ['key' => 'value'],
        ]);

        $this->telegramUserStateService->updateState($telegramUser, $updatedState, ['new_key' => 'new_value']);

        $telegramUserState->refresh();
        $this->assertEquals($updatedState, $telegramUserState->state);
        $this->assertEquals(['new_key' => 'new_value'], $telegramUserState->data);
    }

    public function testUpdateOrCreateStateByTelegramIdCreatesNew()
    {
        $telegramUser = TelegramUser::factory()->create();
        $state = TelegramState::AUTH;

        $this->telegramUserStateService->updateOrCreateStateByTelegramId($telegramUser->telegram_id, $state,
            ['key' => 'value']);

        $this->assertDatabaseHas('telegram_user_states', [
            'telegram_user_id' => $telegramUser->id,
            'state' => $state->value,
            'data' => json_encode(['key' => 'value']),
        ]);
    }

    public function testUpdateOrCreateStateByTelegramIdUpdatesExisting()
    {
        $telegramUser = TelegramUser::factory()->create();
        $existingState = TelegramState::AUTH;
        $newState = TelegramState::NEW_ACCOUNT;

        TelegramUserState::factory()->create([
            'telegram_user_id' => $telegramUser->id,
            'state' => $existingState->value,
            'data' => ['key' => 'value'],
        ]);

        $this->assertDatabaseHas('telegram_user_states', [
            'telegram_user_id' => $telegramUser->id,
            'state' => $existingState->value,
            'data' => json_encode(['key' => 'value']),
        ]);

        $this->telegramUserStateService->updateOrCreateStateByTelegramId($telegramUser->telegram_id, $newState,
            ['new_key' => 'new_value']);

        $this->assertDatabaseHas('telegram_user_states', [
            'telegram_user_id' => $telegramUser->id,
            'state' => $newState->value,
            'data' => json_encode(['new_key' => 'new_value']),
        ]);
    }

    public function testUpdateOrCreateStateByTelegramUserIdCreatesNew()
    {
        $telegramUser = TelegramUser::factory()->create();
        $state = TelegramState::AUTH;

        $this->telegramUserStateService->updateOrCreateStateByTelegramUserId($telegramUser->id, $state,
            ['key' => 'value']);

        $this->assertDatabaseHas('telegram_user_states', [
            'telegram_user_id' => $telegramUser->id,
            'state' => $state->value,
            'data' => json_encode(['key' => 'value']),
        ]);
    }

    public function testUpdateOrCreateStateByTelegramUserIdUpdatesExisting()
    {
        $telegramUser = TelegramUser::factory()->create();
        $existingState = TelegramState::AUTH;
        $newState = TelegramState::NEW_ACCOUNT;

        TelegramUserState::factory()->create([
            'telegram_user_id' => $telegramUser->id,
            'state' => $existingState->value,
            'data' => ['key' => 'value'],
        ]);

        $this->assertDatabaseHas('telegram_user_states', [
            'telegram_user_id' => $telegramUser->id,
            'state' => $existingState->value,
            'data' => json_encode(['key' => 'value']),
        ]);

        $this->telegramUserStateService->updateOrCreateStateByTelegramUserId($telegramUser->id, $newState,
            ['new_key' => 'new_value']);

        $this->assertDatabaseHas('telegram_user_states', [
            'telegram_user_id' => $telegramUser->id,
            'state' => $newState->value,
            'data' => json_encode(['new_key' => 'new_value']),
        ]);
    }

    public function testResetState()
    {
        $telegramUser = TelegramUser::factory()->create();
        $state = TelegramState::AUTH;

        $telegramUserState = TelegramUserState::factory()->create([
            'telegram_user_id' => $telegramUser->id,
            'state' => $state->value,
            'data' => ['key' => 'value'],
        ]);

        $this->assertDatabaseHas('telegram_user_states', [
            'telegram_user_id' => $telegramUser->id,
            'state' => $state->value,
            'data' => json_encode(['key' => 'value']),
        ]);

        $this->telegramUserStateService->resetState($telegramUser);

        $this->assertDatabaseHas('telegram_user_states', [
            'telegram_user_id' => $telegramUser->id,
            'state' => null,
            'data' => null,
        ]);
    }
}
