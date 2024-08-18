<?php

namespace Tests\Feature\Controllers\Api\Controllers\Api\v1;

use App\Models\Account\Account;
use App\Models\Currency\Currency;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\WithTestUser;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithTestUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTestUser();
    }

    public function testGetAllAccountsForUser()
    {
        Account::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson(route('users.accounts.index', ['user' => $this->user->id]));

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'bank',
                        'balance',
                        'currency',
                        'created_at',
                        'updated_at'
                    ],
                ],
            ]);
    }

    public function testGetOnlyUsersAccounts()
    {
        Account::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        Account::factory()->count(3)->create();

        Sanctum::actingAs($this->user);

        $response = $this->getJson(route('users.accounts.index', ['user' => $this->user->id]));

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function testStore()
    {
        Sanctum::actingAs($this->user);
        $currency = Currency::factory()->createOne();

        $response = $this->postJson(route('users.accounts.store', ['user' => $this->user->id]), [
            'currency_id' => $currency->id,
            'name' => 'My Account',
            'bank' => 'Bank of Laravel',
            'balance' => 1000.00,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'bank',
                    'balance',
                    'currency' => [
                        'id',
                        'code',
                        'name',
                        'symbol',
                    ],
                ],
            ]);

        $this->assertDatabaseHas(Account::class, [
            'name' => 'My Account',
            'bank' => 'Bank of Laravel',
            'balance' => 100000,
            'currency_id' => $currency->id,
        ]);
    }

    public function testShow()
    {
        $account = Account::factory()->for($this->user)->create();

        Sanctum::actingAs($this->user);

        $response = $this->getJson(route('accounts.show', ['account' => $account->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'bank',
                    'balance',
                    'currency' => [
                        'id',
                        'code',
                        'name',
                        'symbol',
                    ],
                ],
            ]);
    }

    public function testShowOnlyUsersAccount()
    {
        $account = Account::factory()->create();

        Sanctum::actingAs($this->user);

        $response = $this->getJson(route('accounts.show', ['account' => $account->id]));

        $response->assertForbidden();
    }

    public function testUpdate()
    {
        $currency = Currency::factory()->createOne();
        $account = Account::factory()->for($this->user)->create();

        Sanctum::actingAs($this->user);

        $response = $this->patchJson(route('accounts.update', ['account' => $account->id]), [
            'currency_id' => $currency->id,
            'name' => 'Updated Account',
            'bank' => 'Updated Bank',
            'balance' => 500.00,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'bank',
                    'balance',
                    'currency' => [
                        'id',
                        'code',
                        'name',
                        'symbol',
                    ],
                ],
            ]);

        $this->assertDatabaseHas(Account::class, [
            'id' => $account->id,
            'name' => 'Updated Account',
            'bank' => 'Updated Bank',
            'balance' => 50000,
            'currency_id' => $currency->id,
        ]);
    }

    public function testUpdateOnlyUsersAccount()
    {
        $currency = Currency::factory()->createOne();
        $account = Account::factory()->create();

        Sanctum::actingAs($this->user);

        $response = $this->patchJson(route('accounts.update', ['account' => $account->id]), [
            'currency_id' => $currency->id,
            'name' => 'Updated Account',
            'bank' => 'Updated Bank',
            'balance' => 500.00,
        ]);

        $response->assertForbidden();
    }

    public function testDestroy()
    {
        $account = Account::factory()->for($this->user)->create();

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson(route('accounts.destroy', ['account' => $account->id]));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Account deleted successfully',
            ]);

        $this->assertDatabaseMissing(Account::class, [
            'id' => $account->id,
        ]);
    }

    public function testDestroyOnlyUsersAccount()
    {
        $account = Account::factory()->create();

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson(route('accounts.destroy', ['account' => $account->id]));

        $response->assertForbidden();
    }

    public function testBalance()
    {
        Account::factory()->for($this->user)->create(['balance' => 1000]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson(route('users.accounts.balance', ['user' => $this->user->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'balance',
                    'currency' => [
                        'id',
                        'code',
                        'name',
                        'symbol',
                    ],
                ],
            ]);
    }

    public function testBalanceOnlyUsersAccount()
    {
        Account::factory()->create(['balance' => 1000]);
        Account::factory()->for($this->user)->create(['balance' => 3000]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson(route('users.accounts.balance', ['user' => $this->user->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'balance',
                    'currency' => [
                        'id',
                        'code',
                        'name',
                        'symbol',
                    ],
                ],
            ]);

        $response->assertJsonFragment([
            'balance' => 3000,
        ]);
    }
}
