<?php

namespace Tests\Unit\Services\Transaction;

use App\Enums\Category\CategoryTransactionType;
use App\Models\Account\Account;
use App\Models\Category\Category;
use App\Models\Transaction\Transaction;
use App\Services\Transaction\TelegramTransactionService;
use App\Services\Transaction\TransactionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class TelegramTransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    private TransactionService $transactionService;
    private TelegramTransactionService $telegramTransactionService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transactionService = Mockery::mock(TransactionService::class);
        $this->telegramTransactionService = new TelegramTransactionService($this->transactionService);
    }

    public function testCreateTelegramTransaction()
    {
        $account = Account::factory()->create();

        $transaction = $this->telegramTransactionService->createTelegramTransaction($account->id);

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals($account->id, $transaction->account_id);
        $this->assertEquals(0, $transaction->amount);
        $this->assertEquals('Telegram transaction', $transaction->comment);
        $this->assertFalse($transaction->isFinished);
        $this->assertEquals(CategoryTransactionType::EXPENSE, $transaction->type);
    }

    public function testSetTransactionType()
    {
        $transaction = Transaction::factory()->create();
        $newType = CategoryTransactionType::INCOME;

        $this->transactionService
            ->shouldReceive('getTransactionById')
            ->once()
            ->with($transaction->id)
            ->andReturn($transaction);

        $updatedTransaction = $this->telegramTransactionService->setTransactionType($transaction->id, $newType);

        $this->assertInstanceOf(Transaction::class, $updatedTransaction);
        $this->assertEquals($newType, $updatedTransaction->type);
    }

    public function testAddTransactionCategory()
    {
        $transaction = Transaction::factory()->create();
        $category = Category::factory()->create();

        $this->transactionService
            ->shouldReceive('getTransactionById')
            ->once()
            ->with($transaction->id)
            ->andReturn($transaction);

        $updatedTransaction = $this->telegramTransactionService->addTransactionCategory($transaction->id,
            $category->id);

        $this->assertInstanceOf(Transaction::class, $updatedTransaction);
        $this->assertTrue($updatedTransaction->categories->contains($category));
    }

    public function testRemoveTransactionCategory()
    {
        $category = Category::factory()->create();
        $transaction = Transaction::factory()->hasAttached($category)->create();

        $this->transactionService
            ->shouldReceive('getTransactionById')
            ->once()
            ->with($transaction->id)
            ->andReturn($transaction);

        $updatedTransaction = $this->telegramTransactionService->removeTransactionCategory($transaction->id,
            $category->id);

        $this->assertInstanceOf(Transaction::class, $updatedTransaction);
        $this->assertFalse($updatedTransaction->categories->contains($category));
    }

    public function testSetTransactionAmount()
    {
        $transaction = Transaction::factory()->create();
        $newAmount = 1500;

        $this->transactionService
            ->shouldReceive('getTransactionById')
            ->once()
            ->with($transaction->id)
            ->andReturn($transaction);

        $updatedTransaction = $this->telegramTransactionService->setTransactionAmount($transaction->id, $newAmount);

        $this->assertInstanceOf(Transaction::class, $updatedTransaction);
        $this->assertEquals($newAmount, $updatedTransaction->amount);
    }

    public function testSetTransactionComment()
    {
        $transaction = Transaction::factory()->create();
        $newComment = 'Updated Comment';

        $this->transactionService
            ->shouldReceive('getTransactionById')
            ->once()
            ->with($transaction->id)
            ->andReturn($transaction);

        $updatedTransaction = $this->telegramTransactionService->setTransactionComment($transaction->id, $newComment);

        $this->assertInstanceOf(Transaction::class, $updatedTransaction);
        $this->assertEquals($newComment, $updatedTransaction->comment);
    }

    public function testSetTransactionDate()
    {
        $transaction = Transaction::factory()->create();
        $newDate = Carbon::now()->subDays(2);

        $this->transactionService
            ->shouldReceive('getTransactionById')
            ->once()
            ->with($transaction->id)
            ->andReturn($transaction);

        $updatedTransaction = $this->telegramTransactionService->setTransactionDate($transaction->id, $newDate);

        $this->assertInstanceOf(Transaction::class, $updatedTransaction);
        $this->assertEquals($newDate->toDateString(), $updatedTransaction->date->toDateString());
    }

    public function testFinishTransaction()
    {
        $transaction = Transaction::factory()->create(['isFinished' => false]);

        $this->transactionService
            ->shouldReceive('getTransactionById')
            ->once()
            ->with($transaction->id)
            ->andReturn($transaction);

        $updatedTransaction = $this->telegramTransactionService->finishTransaction($transaction->id);
        
        $this->assertInstanceOf(Transaction::class, $updatedTransaction);
        $this->assertTrue($updatedTransaction->isFinished);
    }

}
