<?php

namespace Tests\Feature\Controllers\Api\Controllers\Api\v1\User;

use App\Models\Account\Account;
use App\Models\Transfer\Transfer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\WithTestUser;

class UserTransferControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithTestUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTestUser();
    }

    public function testGetAllUserTransfers()
    {
        Sanctum::actingAs($this->user);
        $accountFrom = Account::factory()->for($this->user)->create();
        $accountTo = Account::factory()->for($this->user)->create();

        Transfer::factory()->count(15)->create([
            'account_from_id' => $accountFrom->id,
            'account_to_id' => $accountTo->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('users.transfers.index', ['user' => $this->user->id]));

        $response->assertOk();
        $response->assertJsonCount(10, 'data');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'comment',
                    'amount_from',
                    'amount_to',
                    'date',
                    'created_at',
                    'updated_at',
                    'account_from' => [
                        'id',
                        'name',
                        'currency' => [
                            'id',
                            'name',
                            'code',
                        ],
                    ],
                    'account_to' => [
                        'id',
                        'name',
                        'currency' => [
                            'id',
                            'name',
                            'code',
                        ],
                    ],
                ],
            ],
            'links',
            'meta',
        ]);
    }

    public function testGetAlienUserTransaction()
    {
        Sanctum::actingAs($this->user);
        $alienUser = $this->createTestUser();
        $accountFrom = Account::factory()->for($alienUser)->create();
        $accountTo = Account::factory()->for($alienUser)->create();
        Transfer::factory()->count(5)->create([
            'account_from_id' => $accountFrom->id,
            'account_to_id' => $accountTo->id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('users.transfers.index', ['user' => $alienUser->id]));

        $response->assertForbidden();
    }
}
