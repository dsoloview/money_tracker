<?php

namespace Tests\Unit\Services\Transaction;

use App\Data\Transaction\TransactionData;
use App\Enums\Category\CategoryTransactionType;
use App\Models\Account\Account;
use App\Models\Category\Category;
use App\Models\Transaction\Transaction;
use App\Services\Account\AccountBalanceService;
use App\Services\Transaction\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    private AccountBalanceService $accountBalanceService;
    private TransactionService $transactionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accountBalanceService = Mockery::mock(AccountBalanceService::class);
        $this->transactionService = app(TransactionService::class);
    }

    public function testGetAccountTransactions()
    {
        $account = Account::factory()->has(Transaction::factory()->count(2))->create();

        $result = $this->transactionService->getAccountTransactions($account);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertContainsOnlyInstancesOf(Transaction::class, $result);
        $this->assertCount(2, $result);
    }

    public function testGetTransactionById()
    {
        $transaction = Transaction::factory()->create();

        $result = $this->transactionService->getTransactionById($transaction->id);

        $this->assertInstanceOf(Transaction::class, $result);
        $this->assertEquals($transaction->id, $result->id);
    }

    public function testGetAccountTransactionsPaginated()
    {
        $account = Account::factory()->has(Transaction::factory()->count(15))->create();

        $result = $this->transactionService->getAccountTransactionsPaginated($account);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(10, $result->items());
    }

    public function testCreateTransactionForAccount()
    {
        $account = Account::factory()->create();
        $categories = Category::factory()->count(2)->create();

        $data = new TransactionData(
            comment: 'Test transaction',
            amount: 100,
            categories_ids: $categories->pluck('id')->toArray(),
            type: CategoryTransactionType::INCOME,
        );

        $this->accountBalanceService
            ->shouldReceive('increaseAccountBalance');

        $transaction = $this->transactionService->createTransactionForAccount($account, $data);

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals(100, $transaction->amount);
        $this->assertEquals(CategoryTransactionType::INCOME, $transaction->type);
        $this->assertEquals('Test transaction', $transaction->comment);
        $this->assertCount(2, $transaction->categories);
    }

    public function testSyncAccountBalanceForNewIncomeTransaction()
    {
        $account = Account::factory()->create([
            'balance' => 100
        ]);
        $transaction = Transaction::factory()->make([
            'amount' => 100,
            'type' => CategoryTransactionType::INCOME
        ]);


        $this->transactionService->syncAccountBalanceForNewTransaction($transaction, $account);

        $this->assertEquals(200, $account->balance);
    }

    public function testSyncAccountBalanceForNewExpenseTransaction()
    {
        $account = Account::factory()->create([
            'balance' => 100
        ]);
        $transaction = Transaction::factory()->make([
            'amount' => 100,
            'type' => CategoryTransactionType::EXPENSE
        ]);

        $this->transactionService->syncAccountBalanceForNewTransaction($transaction, $account);

        $this->assertEquals(0, $account->balance);
    }

    public function testUpdateTransaction()
    {
        $transaction = Transaction::factory()->create([
            'amount' => 100,
            'type' => CategoryTransactionType::INCOME
        ]);

        $categories = Category::factory()->count(2)->create();

        $data = new TransactionData(
            comment: 'Updated transaction',
            amount: 150,
            categories_ids: $categories->pluck('id')->toArray(),
            type: CategoryTransactionType::EXPENSE,
        );

        $this->accountBalanceService
            ->shouldReceive('updateAccountBalanceWhenTransactionUpdated');

        $updatedTransaction = $this->transactionService->updateTransaction($transaction, $data);

        $this->assertInstanceOf(Transaction::class, $updatedTransaction);
        $this->assertEquals(150, $updatedTransaction->amount);
        $this->assertEquals(CategoryTransactionType::EXPENSE, $updatedTransaction->type);
        $this->assertEquals('Updated transaction', $updatedTransaction->comment);
    }

    public function testDeleteTransaction()
    {
        $transaction = Transaction::factory()->create([
            'amount' => 100,
            'type' => CategoryTransactionType::INCOME
        ]);

        $this->accountBalanceService->shouldReceive('decreaseAccountBalance');

        $this->transactionService->deleteTransaction($transaction);

        $this->assertModelMissing($transaction);
    }

}
