<?php

namespace Tests\Unit\Services\Account;

use App\Data\Transaction\TransactionData;
use App\Enums\Category\CategoryTransactionType;
use App\Models\Account\Account;
use App\Models\Currency\Currency;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Services\Account\AccountBalanceService;
use App\Services\Currency\CurrencyConverterService;
use Mockery;
use Tests\SeededTestCase;

class AccountBalanceServiceTest extends SeededTestCase
{
    public function testItCalculatesUserAccountsBalanceCorrectly()
    {
        $eurCurrency = Currency::where('code', 'EUR')->first();
        $usdCurrency = Currency::where('code', 'USD')->first();

        $user = User::factory()->hasSettings([
            'main_currency_id' => $usdCurrency->id
        ])->create();
        $account1 = Account::factory()->make(['balance' => 100, 'currency_id' => $usdCurrency->id]);
        $account2 = Account::factory()->make(['balance' => 200, 'currency_id' => $eurCurrency->id]);

        $user->accounts = collect([$account1, $account2]);

        $currencyConverterService = Mockery::mock(CurrencyConverterService::class);

        $currencyConverterService->shouldReceive('convert')
            ->with(200, 'EUR', 'USD')
            ->andReturn(220);

        $accountBalanceService = new AccountBalanceService($currencyConverterService);

        $balance = $accountBalanceService->getUserAccountsBalance($user);

        $this->assertEquals(320, $balance);
    }

    public function testItCalculatesAccountBalanceInMainCurrencyCorrectly()
    {
        // Arrange
        $gbpCurrency = Currency::where('code', 'GBP')->first();
        $usdCurrency = Currency::where('code', 'USD')->first();

        $user = User::factory()->hasSettings([
            'main_currency_id' => $usdCurrency->id
        ])->create();

        $account = Account::factory()->make(['balance' => 150, 'currency_id' => $gbpCurrency->id]);

        $currencyConverterService = Mockery::mock(CurrencyConverterService::class);
        $currencyConverterService->shouldReceive('convert')
            ->with(150, 'GBP', 'USD')
            ->andReturn(180);

        $accountBalanceService = new AccountBalanceService($currencyConverterService);

        $balance = $accountBalanceService->getAccountBalanceInMainCurrency($account, $user);

        $this->assertEquals(180, $balance);
    }

    public function testItUpdatesAccountBalanceWhenTransactionIsUpdatedWhenIncome()
    {

        $account = Account::factory()->make(['balance' => 1000]);

        $transaction = Mockery::mock(Transaction::class);
        $transaction->shouldReceive('getAttribute')->with('amount')->andReturn(200);
        $transaction->shouldReceive('getAttribute')->with('type')->andReturn(CategoryTransactionType::INCOME);

        $transactionData = new TransactionData(
            comment: '',
            amount: 150,
            categories_ids: [],
            type: CategoryTransactionType::INCOME,
            date: now(),
        );

        $currencyConverterService = Mockery::mock(CurrencyConverterService::class);
        $accountBalanceService = new AccountBalanceService($currencyConverterService);

        $updatedAccount = $accountBalanceService->updateAccountBalanceWhenTransactionUpdated($account, $transaction,
            $transactionData);

        $this->assertEquals(950, $updatedAccount->balance);
    }

    public function testItUpdatesAccountBalanceWhenTransactionIsUpdatedWhenExpense()
    {
        $account = Account::factory()->make(['balance' => 1000]);

        $transaction = Mockery::mock(Transaction::class);
        $transaction->shouldReceive('getAttribute')->with('amount')->andReturn(200);
        $transaction->shouldReceive('getAttribute')->with('type')->andReturn(CategoryTransactionType::EXPENSE);

        $transactionData = new TransactionData(
            comment: '',
            amount: 150,
            categories_ids: [],
            type: CategoryTransactionType::EXPENSE,
            date: now()
        );

        $currencyConverterService = Mockery::mock(CurrencyConverterService::class);
        $accountBalanceService = new AccountBalanceService($currencyConverterService);

        $updatedAccount = $accountBalanceService->updateAccountBalanceWhenTransactionUpdated($account, $transaction,
            $transactionData);

        $this->assertEquals(1050, $updatedAccount->balance);
    }

    public function testItUpdatesAccountBalanceWhenTransactionIsUpdatedWhenTypeChanged()
    {
        $account = Account::factory()->make(['balance' => 1000]);

        $transaction = Mockery::mock(Transaction::class);
        $transaction->shouldReceive('getAttribute')->with('amount')->andReturn(200);
        $transaction->shouldReceive('getAttribute')->with('type')->andReturn(CategoryTransactionType::INCOME);

        $transactionData = new TransactionData(
            comment: '',
            amount: 150,
            categories_ids: [],
            type: CategoryTransactionType::EXPENSE,
            date: now()
        );

        $currencyConverterService = Mockery::mock(CurrencyConverterService::class);
        $accountBalanceService = new AccountBalanceService($currencyConverterService);

        $updatedAccount = $accountBalanceService->updateAccountBalanceWhenTransactionUpdated($account, $transaction,
            $transactionData);

        $this->assertEquals(650, $updatedAccount->balance);
    }

    public function testItIncreasesAccountBalanceCorrectly()
    {
        $account = Account::factory()->make(['balance' => 1000]);

        $currencyConverterService = Mockery::mock(CurrencyConverterService::class);
        $accountBalanceService = new AccountBalanceService($currencyConverterService);

        $updatedAccount = $accountBalanceService->increaseAccountBalance($account, 200);

        $this->assertEquals(1200, $updatedAccount->balance);
    }

    public function testItDecreasesAccountBalanceCorrectly()
    {
        $account = Account::factory()->make(['balance' => 1000]);

        $currencyConverterService = Mockery::mock(CurrencyConverterService::class);
        $accountBalanceService = new AccountBalanceService($currencyConverterService);

        $updatedAccount = $accountBalanceService->decreaseAccountBalance($account, 200);

        $this->assertEquals(800, $updatedAccount->balance);
    }
}
