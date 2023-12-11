<?php

namespace Tests\Feature\Controller\Api\v1\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register()
    {
        $data = [
            'email' => 'test@test.ru',
            'password' => '12345678',
        ];

        $response = $this->postJson('/api/v1/auth/register', $data);

        $response->assertOk();

        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'user' => [
                'id',
                'email',
                'roles',
            ],
        ]);

    }
}
