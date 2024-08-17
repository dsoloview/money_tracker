<?php

namespace Tests\Feature\Controllers\Api\Controllers\Api;

use App\Models\Account\Account;
use App\Models\Currency\Currency;
use App\Models\User;
use App\Models\UserSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        UserSettings::factory()->createOne([
            'user_id' => $this->user->id,
        ]);
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

}
