<?php

namespace Tests\Feature\Controllers\Api\Controllers\Api\v1;

use App\Models\Currency\Currency;
use App\Models\Language\Language;
use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\WithTestUser;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithTestUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTestUser();
    }

    public function testGetAllUsers()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson(route('users.index'));

        $response->assertForbidden();
    }

    public function testCreateUser()
    {
        Sanctum::actingAs($this->user);
        $currency = Currency::factory()->createOne();
        $language = Language::factory()->createOne();

        $response = $this->postJson(route('users.store'), [
            'name' => 'Test User',
            'email' => 'qwerty@qwerty.com',
            'password' => 'qwerty123',
            'password_confirmation' => 'qwerty123',
            'settings' => [
                'main_currency_id' => $currency->id,
                'language_id' => $language->id,
            ],
        ]);

        $response->assertForbidden();
    }

    public function testShowUser()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson(route('users.show', ['user' => $this->user->id]));

        $response->assertOk();
    }

    public function testNotShowAnotherUser()
    {
        Sanctum::actingAs($this->user);
        $anotherUser = User::factory()->hasSettings()->createOne();

        $response = $this->getJson(route('users.show', ['user' => $anotherUser->id]));

        $response->assertForbidden();
    }

    public function testUpdateUser()
    {
        Sanctum::actingAs($this->user);
        $language = Language::factory()->createOne();
        $currency = Currency::factory()->createOne();

        $response = $this->putJson(route('users.update', ['user' => $this->user->id]), [
            'name' => 'Test User',
            'email' => 'qwerty123@qwerty123.com',
            'settings' => [
                'language_id' => $language->id,
                'main_currency_id' => $currency->id,
            ],
        ]);

        $response->assertOk();

        $this->assertDatabaseHas(User::class, [
            'id' => $this->user->id,
            'name' => 'Test User',
            'email' => 'qwerty123@qwerty123.com',
        ]);

        $this->assertDatabaseHas(UserSettings::class, [
            'user_id' => $this->user->id,
            'language_id' => $language->id,
            'main_currency_id' => $currency->id,
        ]);
    }

    public function testNotUpdateAnotherUser()
    {
        Sanctum::actingAs($this->user);
        $anotherUser = User::factory()->hasSettings()->createOne();
        $language = Language::factory()->createOne();
        $currency = Currency::factory()->createOne();

        $response = $this->putJson(route('users.update', ['user' => $anotherUser->id]), [
            'name' => 'Test User',
            'email' => 'qwerty123@qwerty123.com',
            'settings' => [
                'language_id' => $language->id,
                'main_currency_id' => $currency->id,
            ],
        ]);

        $response->assertForbidden();
    }

    public function testUpdateUserSettings()
    {
        Sanctum::actingAs($this->user);
        $language = Language::factory()->createOne();
        $currency = Currency::factory()->createOne();

        $response = $this->patchJson(route('users.update_settings', ['user' => $this->user->id]), [
            'language_id' => $language->id,
            'main_currency_id' => $currency->id,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas(UserSettings::class, [
            'user_id' => $this->user->id,
            'language_id' => $language->id,
            'main_currency_id' => $currency->id,
        ]);
    }

    public function testNotUpdateAnotherUserSettings()
    {
        Sanctum::actingAs($this->user);
        $anotherUser = User::factory()->hasSettings()->createOne();
        $language = Language::factory()->createOne();
        $currency = Currency::factory()->createOne();

        $response = $this->patchJson(route('users.update_settings', ['user' => $anotherUser->id]), [
            'language_id' => $language->id,
            'main_currency_id' => $currency->id,
        ]);

        $response->assertForbidden();
    }

    public function testUpdateUserPassword()
    {
        $user = User::factory()->hasSettings()->createOne([
            'password' => 'password',
        ]);

        Sanctum::actingAs($user);

        $response = $this->patchJson(route('users.update_password', ['user' => $user->id]), [
            'current_password' => 'password',
            'password' => 'qwerty123',
            'password_confirmation' => 'qwerty123',
        ]);

        $response->assertOk();

        $this->assertTrue(\Hash::check('qwerty123', $user->fresh()->password));
    }

    public function testNotUpdateAnotherUserPassword()
    {
        Sanctum::actingAs($this->user);
        $anotherUser = User::factory()->hasSettings()->createOne([
            'password' => 'password',
        ]);

        $response = $this->patchJson(route('users.update_password', ['user' => $anotherUser->id]), [
            'current_password' => 'password',
            'password' => 'qwerty123',
            'password_confirmation' => 'qwerty123',
        ]);

        $response->assertStatus(422);
    }

    public function testDeleteSelfUser()
    {
        $user = User::factory()->hasSettings()->createOne();
        Sanctum::actingAs($user);

        $response = $this->deleteJson(route('users.destroy', ['user' => $user->id]));

        $response->assertInternalServerError();
    }

    public function testDeleteAnotherUser()
    {
        Sanctum::actingAs($this->user);
        $anotherUser = User::factory()->hasSettings()->createOne();

        $response = $this->deleteJson(route('users.destroy', ['user' => $anotherUser->id]));

        $response->assertOk();
        $this->assertDatabaseMissing(User::class, ['id' => $anotherUser->id]);
        $this->assertDatabaseMissing(UserSettings::class, ['user_id' => $anotherUser->id]);
    }
}
