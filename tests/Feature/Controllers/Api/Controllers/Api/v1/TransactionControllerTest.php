<?php

namespace Tests\Feature\Controllers\Api\Controllers\Api\v1;

use App\Enums\Category\CategoryTransactionType;
use App\Models\Account\Account;
use App\Models\Category\Category;
use App\Models\Transaction\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\WithTestUser;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithTestUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTestUser();
    }

    public function testGetAllTransactionsForAccount()
    {
        Sanctum::actingAs($this->user);
        $account = Account::factory()->for($this->user)->create();
        Transaction::factory()->count(10)->for($account)->create();

        $response = $this->actingAs($this->user)->getJson(route('accounts.transactions.index', $account));

        $response->assertOk();
        $response->assertJsonCount(10, 'data');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'account_id',
                    'comment',
                    'amount',
                    'user_currency_amount',
                    'account',
                    'type',
                    'categories',
                    'date',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function testNotShowAlienTransactions()
    {
        Sanctum::actingAs($this->user);
        $account = Account::factory()->create();

        $response = $this->actingAs($this->user)->getJson(route('accounts.transactions.index', $account));

        $response->assertForbidden();
    }

    public function testStoreTransaction()
    {
        $account = Account::factory()->for($this->user)->create();
        $category = Category::factory()->for($this->user)->createOne([
            'type' => CategoryTransactionType::EXPENSE->value,
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->postJson(route('accounts.transactions.store', $account), [
            'account_id' => $account->id,
            'comment' => 'Test transaction',
            'amount' => 100,
            'categories_ids' => [$category->id],
            'date' => now()->toDateString(),
            'type' => CategoryTransactionType::EXPENSE->value,
        ]);

        $response->assertCreated();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'account_id',
                'comment',
                'amount',
                'user_currency_amount',
                'account',
                'type',
                'categories',
                'date',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function testShowTransaction()
    {
        $account = Account::factory()->for($this->user)->create();
        $transaction = Transaction::factory()->for($account)->create();

        Sanctum::actingAs($this->user);

        $response = $this->getJson(route('transactions.show', $transaction));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'account_id',
                'comment',
                'amount',
                'user_currency_amount',
                'account',
                'type',
                'categories',
                'date',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function testNotShowAlienTransaction()
    {
        $transaction = Transaction::factory()->create();

        Sanctum::actingAs($this->user);

        $response = $this->getJson(route('transactions.show', $transaction));

        $response->assertForbidden();
    }

    public function testTransactionUpdate()
    {
        $account = Account::factory()->for($this->user)->create();
        $transaction = Transaction::factory()->for($account)->create();
        $category = Category::factory()->for($this->user)->createOne([
            'type' => CategoryTransactionType::EXPENSE->value,
        ]);

        Sanctum::actingAs($this->user);

        $dateTime = now();
        $response = $this->putJson(route('transactions.update', $transaction), [
            'account_id' => $account->id,
            'comment' => 'Test transaction',
            'amount' => 100,
            'categories_ids' => [$category->id],
            'date' => $dateTime,
            'type' => CategoryTransactionType::EXPENSE->value,
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'account_id',
                'comment',
                'amount',
                'user_currency_amount',
                'account',
                'type',
                'date',
                'created_at',
                'updated_at',
            ],
        ]);
        $this->assertDatabaseHas(Transaction::class, [
            'id' => $transaction->id,
            'account_id' => $account->id,
            'comment' => 'Test transaction',
            'amount' => 10000,
            'date' => $dateTime,
            'type' => CategoryTransactionType::EXPENSE->value,
        ]);
    }

    public function testNotUpdateAlienTransaction()
    {
        $transaction = Transaction::factory()->create();
        $category = Category::factory()->for($this->user)->createOne([
            'type' => CategoryTransactionType::EXPENSE->value,
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->putJson(route('transactions.update', $transaction), [
            'account_id' => $transaction->account_id,
            'comment' => 'Test transaction',
            'amount' => 100,
            'categories_ids' => [$category->id],
            'date' => now()->toDateString(),
            'type' => CategoryTransactionType::EXPENSE->value,
        ]);

        $response->assertForbidden();
    }

    public function testTransactionDelete()
    {
        $account = Account::factory()->for($this->user)->create();
        $transaction = Transaction::factory()->for($account)->create();

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson(route('transactions.destroy', $transaction));

        $response->assertOk();
        $this->assertDatabaseMissing(Transaction::class, [
            'id' => $transaction->id,
        ]);
    }

    public function testNotDeleteAlienTransaction()
    {
        $transaction = Transaction::factory()->create();

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson(route('transactions.destroy', $transaction));

        $response->assertForbidden();
    }

    public function testTransactionRequestValidation()
    {
        $account = Account::factory()->for($this->user)->create();
        $category = Category::factory()->for($this->user)->createOne([
            'type' => CategoryTransactionType::EXPENSE->value,
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->postJson(route('accounts.transactions.store', $account), [
            'account_id' => $account->id,
            'comment' => 'Test transaction',
            'amount' => 'string',
            'categories_ids' => [$category->id],
            'date' => now()->toDateString(),
            'type' => CategoryTransactionType::EXPENSE->value,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'amount',
        ]);
    }
}
