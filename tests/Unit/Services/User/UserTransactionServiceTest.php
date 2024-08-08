<?php

namespace Tests\Unit\Services\User;

use App\Data\Transaction\TransactionsInfoData;
use App\Http\Resources\Currency\CurrencyResource;
use App\Models\Account\Account;
use App\Models\Currency\Currency;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Models\UserSettings;
use App\Repositories\Transaction\TransactionRepository;
use App\Services\Currency\CurrencyConverterService;
use App\Services\User\Transaction\UserTransactionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Mockery;
use Tests\SeededTestCase;

class UserTransactionServiceTest extends SeededTestCase
{
    use RefreshDatabase;

    private UserTransactionService $userTransactionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userTransactionService = app(UserTransactionService::class);
    }

    public function testGetUserTransactionsPaginated()
    {
        $user = User::factory()->create();

        Account::factory()->hasTransactions(5)->create([
            'user_id' => $user->id,
        ]);
        Account::factory()->hasTransactions(10)->create([
            'user_id' => $user->id,
        ]);

        $paginatedTransactions = $this->userTransactionService->getUserTransactionsPaginated($user);


        $this->assertInstanceOf(LengthAwarePaginator::class, $paginatedTransactions);
        $this->assertCount(10, $paginatedTransactions->items());
        $this->assertTrue($paginatedTransactions->hasPages());
        $this->assertTrue($paginatedTransactions->onFirstPage());

        foreach ($paginatedTransactions as $transaction) {
            $this->assertTrue($transaction->relationLoaded('account'));
            $this->assertTrue($transaction->account->relationLoaded('currency'));
            $this->assertTrue($transaction->relationLoaded('categories'));
        }
    }

    public function testGetUserTransactionsFilteredByDates()
    {
        $user = User::factory()->create();
        $currency = Currency::first();
        $account = Account::factory()->create([
            'user_id' => $user->id,
            'currency_id' => $currency->id,
        ]);
        $transactionWithinDateRange = Transaction::factory()->create([
            'account_id' => $account->id,
            'date' => Carbon::now()->subDays(5),
        ]);

        $transactionOutsideDateRange = Transaction::factory()->create([
            'account_id' => $account->id,
            'date' => Carbon::now()->subDays(15),
        ]);

        $fromDate = Carbon::now()->subDays(10);
        $toDate = Carbon::now();

        $filteredTransactions = $this->userTransactionService
            ->getUserTransactionsFilteredByDates($user, $fromDate, $toDate);

        $this->assertInstanceOf(Collection::class, $filteredTransactions);
        $this->assertCount(1, $filteredTransactions);
        $this->assertTrue($filteredTransactions->contains($transactionWithinDateRange));
        $this->assertFalse($filteredTransactions->contains($transactionOutsideDateRange));

        foreach ($filteredTransactions as $transaction) {
            $this->assertTrue($transaction->relationLoaded('account'));
            $this->assertTrue($transaction->account->relationLoaded('currency'));
            $this->assertTrue($transaction->relationLoaded('categories'));
        }
    }

    public function testGetMinTransactionAmount()
    {
        $user = User::factory()->create();
        $currency = Currency::first();

        $account = Account::factory()->create([
            'user_id' => $user->id,
            'currency_id' => $currency->id,
        ]);

        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 1000,
        ]);
        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 500,
        ]);

        $minAmount = $this->userTransactionService->getMinTransactionAmount($user);

        $this->assertEquals(500, $minAmount);
    }

    public function testGetMaxTransactionAmount()
    {
        $user = User::factory()->create();
        $currency = Currency::first();

        $account = Account::factory()->create([
            'user_id' => $user->id,
            'currency_id' => $currency->id,
        ]);

        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 1000,
        ]);
        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 500,
        ]);

        $minAmount = $this->userTransactionService->getMaxTransactionAmount($user);

        $this->assertEquals(1000, $minAmount);
    }

    public function testGetTransactionsInfoForUser()
    {
        $usdCurrency = Currency::where('code', 'USD')->first();
        $user = User::factory()->create();

        UserSettings::factory()->create([
            'user_id' => $user->id,
            'main_currency_id' => $usdCurrency->id,
        ]);

        $currency = Currency::where('code', 'USD')->first();
        $repository = Mockery::mock(TransactionRepository::class);
        $this->app->instance(TransactionRepository::class, $repository);

        $data = [
            [
                'total_expense' => 2000,
                'total_income' => 5000,
                'min_transaction' => 1000,
                'max_transaction' => 3000,
                'currency' => $currency->code,
            ],
            [
                'total_expense' => 1000,
                'total_income' => 2000,
                'min_transaction' => 500,
                'max_transaction' => 4000,
                'currency' => $currency->code,
            ],
        ];

        $repository->shouldReceive('getFilteredTransactionsInfoForUser')
            ->once()
            ->with($user->id)
            ->andReturn($data);

        $currencyConverterService = Mockery::mock(CurrencyConverterService::class);
        $this->app->instance(CurrencyConverterService::class, $currencyConverterService);

        $currencyConverterService->shouldReceive('convertToUserCurrency')
            ->andReturnUsing(function ($amount, $currency, $user) {
                return $amount;
            });

        $transactionInfo = $this->userTransactionService->getTransactionsInfoForUser($user);

        $this->assertInstanceOf(TransactionsInfoData::class, $transactionInfo);
        $this->assertInstanceOf(CurrencyResource::class, $transactionInfo->currency);
        $this->assertEquals(500, $transactionInfo->min_transaction);
        $this->assertEquals(4000, $transactionInfo->max_transaction);
        $this->assertEquals(3000, $transactionInfo->total_expense);
        $this->assertEquals(7000, $transactionInfo->total_income);
    }
}
