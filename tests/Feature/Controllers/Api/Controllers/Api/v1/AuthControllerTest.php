<?php

namespace Tests\Feature\Controllers\Api\Controllers\Api\v1;

use App\Models\Currency\Currency;
use App\Models\Language\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SeededTestCase;

class AuthControllerTest extends SeededTestCase
{
    use RefreshDatabase;

    public function testRegister()
    {
        $currency = Currency::factory()->create();
        $language = Language::factory()->create();

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'test@test.ru',
            'password' => 'password',
            'password_confirmation' => 'password',
            'settings' => [
                'main_currency_id' => $currency->id,
                'language_id' => $language->id,
            ],
        ]);

        $response->assertOk();

        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'user' => [
                'id',
                'name',
                'email',
                'balance',
                'created_at',
                'updated_at',
                'roles',
                'settings' => [
                    'id',
                    'main_currency',
                    'language',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);

        $this->assertDatabaseHas(User::class, [
            'name' => 'John Doe',
            'email' => 'test@test.ru'

        ]);
    }

    public function testRegisterWrongRequest()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'testtest.ru',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'email',
            'settings'
        ]);
    }

    public function testLogin()
    {
        User::factory()->hasSettings()->createOne([
            'email' => 'test@test.ru',
            'password' => 'password'
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@test.ru',
            'password' => 'password',
        ]);

        $response->assertOk();

        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'user' => [
                'id',
                'name',
                'email',
                'balance',
                'created_at',
                'updated_at',
                'roles',
                'settings' => [
                    'id',
                    'main_currency',
                    'language',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function testLoginWrongPassword()
    {
        User::factory()->hasSettings()->createOne([
            'email' => 'test@test.ru',
            'password' => 'password'
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@test.ru',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertJsonFragment([
            'message' => 'Invalid credentials'
        ]);
    }

    public function testLogout()
    {
        $user = User::factory()->hasSettings()->createOne([
            'email' => 'test@test.ru',
            'password' => 'password'
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@test.ru',
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);

        $token = $response->json('access_token');

        $response = $this->withHeader('Authorization', 'Bearer '.$token)->postJson('/api/v1/auth/logout');

        $response->assertOk();
        $response->assertJsonFragment([
            'success' => true
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);
    }
}
