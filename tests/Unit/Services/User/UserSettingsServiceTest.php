<?php

namespace Tests\Unit\Services\User;

use App\Data\User\Settings\UserSettingsData;
use App\Models\User;
use App\Models\UserSettings;
use App\Services\User\Settings\UserSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SeededTestCase;

class UserSettingsServiceTest extends SeededTestCase
{
    use RefreshDatabase;

    private UserSettingsService $userSettingsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userSettingsService = app(UserSettingsService::class);
    }

    public function testCreateSettingsForUser()
    {
        $user = User::factory()->create();

        $userSettingData = new UserSettingsData(
            main_currency_id: 1,
            language_id: 1
        );

        $result = $this->userSettingsService->createSettingsForUser($user, $userSettingData);

        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($userSettingData->main_currency_id, $result->main_currency_id);
        $this->assertEquals($userSettingData->language_id, $result->language_id);

        $this->assertDatabaseHas(UserSettings::class, [
            'user_id' => $user->id,
            'main_currency_id' => $userSettingData->main_currency_id,
            'language_id' => $userSettingData->language_id,
        ]);
    }

    public function testUpdateSettingsForUser()
    {
        $user = User::factory()->create();
        $userSettings = UserSettings::factory()->create([
            'user_id' => $user->id,
            'main_currency_id' => 1,
            'language_id' => 1,
        ]);

        $userSettingData = new UserSettingsData(
            main_currency_id: 2,
            language_id: 2
        );

        $result = $this->userSettingsService->updateSettingsForUser($user, $userSettingData);

        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($userSettingData->main_currency_id, $result->main_currency_id);
        $this->assertEquals($userSettingData->language_id, $result->language_id);

        $this->assertDatabaseHas(UserSettings::class, [
            'user_id' => $user->id,
            'main_currency_id' => $userSettingData->main_currency_id,
            'language_id' => $userSettingData->language_id,
        ]);
    }
}
