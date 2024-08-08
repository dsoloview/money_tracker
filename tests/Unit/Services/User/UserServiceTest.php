<?php

namespace Tests\Unit\Services\User;

use App\Data\User\Settings\UserSettingsData;
use App\Data\User\UserCreateData;
use App\Data\User\UserUpdateData;
use App\Data\User\UserUpdatePasswordData;
use App\Enums\Role\Roles;
use App\Models\User;
use App\Models\UserSettings;
use App\Services\Category\CategoryService;
use App\Services\User\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Tests\SeededTestCase;

class UserServiceTest extends SeededTestCase
{
    use RefreshDatabase;

    private CategoryService $categoryService;
    private UserService $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryService = app(CategoryService::class);
        $this->userService = app(UserService::class);
    }

    public function testIndex()
    {
        User::factory()->count(3)->create();

        $result = $this->userService->index();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
        $this->assertContainsOnlyInstancesOf(User::class, $result);
    }

    public function testIndexPaginated()
    {
        User::factory()->count(15)->create();

        $result = $this->userService->indexPaginated();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(10, $result->items());
        $this->assertContainsOnlyInstancesOf(User::class, $result->items());
    }

    public function testStore()
    {
        $settingsData = new UserSettingsData(
            main_currency_id: 1,
            language_id: 1
        );

        $data = new UserCreateData(
            name: 'Test User',
            email: 'test@example.com',
            password: bcrypt('password'),
            settings: $settingsData
        );

        $user = $this->userService->store($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
        $this->assertTrue($user->hasRole(Roles::user->value));

        $this->assertInstanceOf(UserSettings::class, $user->settings);
        $this->assertEquals(1, $user->settings->language_id);
        $this->assertEquals(1, $user->settings->main_currency_id);

        $defaultCategories = $this->categoryService->getDefaultForLanguage($user->language);
        $this->assertCount($defaultCategories->count(), $user->categories);
    }

    public function testShow()
    {

        $user = User::factory()->create();

        $settings = UserSettings::factory()->create([
            'user_id' => $user->id,
            'language_id' => 1,
            'main_currency_id' => 1
        ]);


        $user->settings()->save($settings);
        $user->assignRole(Roles::user->value);


        $result = $this->userService->show($user);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($user->id, $result->id);
        $this->assertTrue($result->relationLoaded('roles'));
        $this->assertTrue($result->relationLoaded('settings'));
    }

    public function testUpdate()
    {
        $user = User::factory()->create();
        $user->settings()->create([
            'language_id' => 1,
            'main_currency_id' => 1
        ]);

        $settingsData = new UserSettingsData(
            main_currency_id: 2,
            language_id: 2
        );

        $data = new UserUpdateData(
            email: 'updated@example.com',
            name: 'Updated User',
            settings: $settingsData
        );

        $updatedUser = $this->userService->update($data, $user);

        $this->assertInstanceOf(User::class, $updatedUser);
        $this->assertEquals('Updated User', $updatedUser->name);
        $this->assertEquals('updated@example.com', $updatedUser->email);

        $this->assertEquals(2, $updatedUser->settings->language_id);
        $this->assertEquals(2, $updatedUser->settings->main_currency_id);
    }

    public function testUpdateSettings()
    {
        $user = User::factory()->create();
        $user->settings()->create([
            'language_id' => 1,
            'main_currency_id' => 1
        ]);

        $settingsData = new UserSettingsData(
            main_currency_id: 2,
            language_id: 2
        );

        $updatedSettings = $this->userService->updateSettings($settingsData, $user);

        $this->assertInstanceOf(UserSettings::class, $updatedSettings);
        $this->assertEquals(2, $updatedSettings->language_id);
        $this->assertEquals(2, $updatedSettings->main_currency_id);
    }

    public function testUpdatePassword()
    {
        $user = User::factory()->create(['password' => bcrypt('old_password')]);

        $data = new UserUpdatePasswordData(
            current_password: 'old_password',
            password: 'new_password',
            password_confirmation: 'new_password'
        );

        $result = $this->userService->updatePassword($data, $user);

        $this->assertTrue($result);
        $this->assertTrue(password_verify('new_password', $user->password));
    }

    public function testDestroy()
    {
        $currentUser = User::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($currentUser);

        $result = $this->userService->destroy($user);

        $this->assertTrue($result);
        $this->assertModelMissing($user);
    }

    public function testDestroyFailed()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You cannot delete yourself');

        $this->userService->destroy($user);
    }
}
