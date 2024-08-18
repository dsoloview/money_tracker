<?php

namespace Tests\Feature\Controllers\Api\Controllers\Api\v1;

use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\WithTestUser;

class RoleControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithTestUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);
        $this->setUpTestUser();
    }

    public function testGetAllRoles()
    {
        Sanctum::actingAs($this->user);
        $response = $this->getJson(route('roles.index'));

        $response->assertStatus(403);
    }

    public function testGetSingleRole()
    {
        Sanctum::actingAs($this->user);
        $response = $this->getJson(route('roles.show', 1));

        $response->assertStatus(403);
    }
}
