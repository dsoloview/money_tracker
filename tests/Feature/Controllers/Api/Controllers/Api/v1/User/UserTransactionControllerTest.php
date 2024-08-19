<?php

namespace Tests\Feature\Controllers\Api\Controllers\Api\v1\User;

use App\Models\Account\Account;
use App\Models\Transaction\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\WithTestUser;

class UserTransactionControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithTestUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTestUser();
    }

    public function testGetAllUserTransactions()
    {
        Sanctum::actingAs($this->user);
        $account = Account::factory()->for($this->user)->create();
        $account2 = Account::factory()->for($this->user)->create();
        $alienAccount = Account::factory()->create();

        Transaction::factory()->count(5)->for($account)->create();
        Transaction::factory()->count(10)->for($account2)->create();
        Transaction::factory()->count(15)->for($alienAccount)->create();

        $response = $this->actingAs($this->user)
            ->getJson(route('users.transactions.index', ['user' => $this->user->id]));

        $response->assertOk();
        $response->assertJsonCount(10, 'data');

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'account_id',
                    'comment',
                    'amount',
                    'user_currency_amount' => [
                        'amount',
                        'currency',
                    ],
                    'account',
                    'type',
                    'categories',
                    'date',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta',
            'info' => [
                'currency',
                'total_expense',
                'total_income',
                'min_transaction',
                'max_transaction',
            ]
        ]);
    }

    public function testGetAlienUserTransactions()
    {
        Sanctum::actingAs($this->user);
        $alienUser = $this->createTestUser();
        $account = Account::factory()->for($alienUser)->create();
        Transaction::factory()->count(5)->for($account)->create();

        $response = $this->actingAs($this->user)
            ->getJson(route('users.transactions.index', ['user' => $alienUser->id]));

        $response->assertForbidden();
    }

    public function testGetMinMaxTransactionAmounts()
    {
        Sanctum::actingAs($this->user);
        $account = Account::factory()->for($this->user)->create();
        Transaction::factory()->count(5)->for($account)->create(
            ['amount' => 1000]
        );
        Transaction::factory()->for($account)->create(
            ['amount' => 2000]
        );


        $response = $this->actingAs($this->user)
            ->getJson(route('users.transactions.min_max', ['user' => $this->user->id]));

        $response->assertOk();
        $response->assertJson([
            'data' => [
                'min' => 1000,
                'max' => 2000,
            ],
        ]);
    }

    public function testGetMinMaxTransactionAmountsAlienUser()
    {
        Sanctum::actingAs($this->user);
        $alienUser = $this->createTestUser();
        $account = Account::factory()->for($alienUser)->create();
        Transaction::factory()->count(5)->for($account)->create(
            ['amount' => 1000]
        );
        Transaction::factory()->for($account)->create(
            ['amount' => 2000]
        );

        $response = $this->actingAs($this->user)
            ->getJson(route('users.transactions.min_max', ['user' => $alienUser->id]));

        $response->assertForbidden();
    }

    public function testGetTransactionsInfo()
    {
        Sanctum::actingAs($this->user);
        $account = Account::factory()->for($this->user)->create();
        Transaction::factory()->count(5)->for($account)->create();
        Transaction::factory()->for($account)->create();

        $response = $this->actingAs($this->user)
            ->getJson(route('users.transactions.info', ['user' => $this->user->id]));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'currency',
                'total_expense',
                'total_income',
                'min_transaction',
                'max_transaction',
            ],
        ]);
    }

    public function testGetTransactionsInfoAlienUser()
    {
        Sanctum::actingAs($this->user);
        $alienUser = $this->createTestUser();
        $account = Account::factory()->for($alienUser)->create();
        Transaction::factory()->count(5)->for($account)->create();
        Transaction::factory()->for($account)->create();

        $response = $this->actingAs($this->user)
            ->getJson(route('users.transactions.info', ['user' => $alienUser->id]));

        $response->assertForbidden();
    }


}
