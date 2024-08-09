<?php

namespace Tests\Unit\Repositories\Transaction;

use App\Models\Account\Account;
use App\Models\Currency\Currency;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Repositories\Transaction\TransactionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private TransactionRepository $transactionRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transactionRepository = new TransactionRepository();
    }

    public function testGetFilteredTransactionsInfoForUserWithNoTransactions()
    {
        $user = User::factory()->create();

        $result = $this->transactionRepository->getFilteredTransactionsInfoForUser($user->id);

        $this->assertEmpty($result);
    }

    public function testGetFilteredTransactionsInfoForUserWithSingleTransaction()
    {
        $user = User::factory()->create();
        $currency = Currency::factory()->create(['code' => 'USD']);
        $account = Account::factory()->create(['user_id' => $user->id, 'currency_id' => $currency->id]);

        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 50,
            'type' => 'income',
        ]);

        $result = $this->transactionRepository->getFilteredTransactionsInfoForUser($user->id);

        $this->assertCount(1, $result);
        $this->assertEquals('USD', $result[0]['currency']);
        $this->assertEquals(0, $result[0]['total_expense']);
        $this->assertEquals(50, $result[0]['total_income']);
        $this->assertEquals(50, $result[0]['min_transaction']);
        $this->assertEquals(50, $result[0]['max_transaction']);
    }

    public function testGetFilteredTransactionsInfoForUserWithMultipleTransactions()
    {
        $user = User::factory()->create();
        $currency = Currency::factory()->create(['code' => 'USD']);
        $account = Account::factory()->create(['user_id' => $user->id, 'currency_id' => $currency->id]);

        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 50,
            'type' => 'income',
        ]);

        Transaction::factory()->create([
            'account_id' => $account->id,
            'amount' => 20,
            'type' => 'expense',
        ]);

        $result = $this->transactionRepository->getFilteredTransactionsInfoForUser($user->id);

        $this->assertCount(1, $result);
        $this->assertEquals('USD', $result[0]['currency']);
        $this->assertEquals(20, $result[0]['total_expense']);
        $this->assertEquals(50, $result[0]['total_income']);
        $this->assertEquals(20, $result[0]['min_transaction']);
        $this->assertEquals(50, $result[0]['max_transaction']);
    }

    public function testGetFilteredTransactionsInfoForUserWithMultipleCurrencies()
    {
        $user = User::factory()->create();
        $usdCurrency = Currency::factory()->create(['code' => 'USD']);
        $eurCurrency = Currency::factory()->create(['code' => 'EUR']);

        $usdAccount = Account::factory()->create(['user_id' => $user->id, 'currency_id' => $usdCurrency->id]);
        $eurAccount = Account::factory()->create(['user_id' => $user->id, 'currency_id' => $eurCurrency->id]);

        Transaction::factory()->create([
            'account_id' => $usdAccount->id,
            'amount' => 50,
            'type' => 'income',
        ]);

        Transaction::factory()->create([
            'account_id' => $eurAccount->id,
            'amount' => 30,
            'type' => 'expense',
        ]);

        $result = $this->transactionRepository->getFilteredTransactionsInfoForUser($user->id);

        $this->assertCount(2, $result);

        $usdResult = collect($result)->firstWhere('currency', 'USD');
        $this->assertEquals(50, $usdResult['total_income']);
        $this->assertEquals(0, $usdResult['total_expense']);

        $eurResult = collect($result)->firstWhere('currency', 'EUR');
        $this->assertEquals(30, $eurResult['total_expense']);
        $this->assertEquals(0, $eurResult['total_income']);
    }

    public function testGetFilteredTransactionsInfoForUserWithMultipleTransactionsAndAmounts()
    {
        $user = User::factory()->create();
        $currency = Currency::factory()->create(['code' => 'USD']);
        $account = Account::factory()->create(['user_id' => $user->id, 'currency_id' => $currency->id]);

        $transactions = [
            ['amount' => 10, 'type' => 'income'],
            ['amount' => 50, 'type' => 'income'],
            ['amount' => 30, 'type' => 'income'],
            ['amount' => 20, 'type' => 'expense'],
            ['amount' => 40, 'type' => 'expense'],
        ];

        foreach ($transactions as $transaction) {
            Transaction::factory()->create([
                'account_id' => $account->id,
                'amount' => $transaction['amount'],
                'type' => $transaction['type'],
            ]);
        }

        $result = $this->transactionRepository->getFilteredTransactionsInfoForUser($user->id);

        $this->assertCount(1, $result);
        $this->assertEquals('USD', $result[0]['currency']);
        $this->assertEquals(60, $result[0]['total_expense']); // $20 + $40
        $this->assertEquals(90, $result[0]['total_income']); // $10 + $50 + $30
        $this->assertEquals(10, $result[0]['min_transaction']); // Minimum transaction is $10.00
        $this->assertEquals(50, $result[0]['max_transaction']); // Maximum transaction is $50.00
    }

    public function testGetFilteredTransactionsInfoForUserWithLargeNumberOfTransactions()
    {
        $user = User::factory()->create();
        $currency = Currency::factory()->create(['code' => 'USD']);
        $account = Account::factory()->create(['user_id' => $user->id, 'currency_id' => $currency->id]);

        // Creating 100 income transactions with amounts ranging from $1.00 to $100.00
        for ($i = 1; $i <= 100; $i++) {
            Transaction::factory()->create([
                'account_id' => $account->id,
                'amount' => $i,
                'type' => 'income',
            ]);
        }

        // Creating 50 expense transactions with amounts ranging from $1.00 to $50.00
        for ($i = 1; $i <= 50; $i++) {
            Transaction::factory()->create([
                'account_id' => $account->id,
                'amount' => $i,
                'type' => 'expense',
            ]);
        }

        $result = $this->transactionRepository->getFilteredTransactionsInfoForUser($user->id);

        $this->assertCount(1, $result);
        $this->assertEquals('USD', $result[0]['currency']);
        $this->assertEquals(1275, $result[0]['total_expense']); // Sum of first 50 numbers: (50 * 51) / 2 = $1275
        $this->assertEquals(5050, $result[0]['total_income']); // Sum of first 100 numbers: (100 * 101) / 2 = $5050
        $this->assertEquals(1, $result[0]['min_transaction']); // Minimum transaction is $1.00
        $this->assertEquals(100, $result[0]['max_transaction']); // Maximum transaction is $100.00
    }

    public function testGetFilteredTransactionsInfoForUserWithMixedCurrenciesAndTransactions()
    {
        $user = User::factory()->create();
        $usdCurrency = Currency::factory()->create(['code' => 'USD']);
        $eurCurrency = Currency::factory()->create(['code' => 'EUR']);
        $gbpCurrency = Currency::factory()->create(['code' => 'GBP']);

        $usdAccount = Account::factory()->create(['user_id' => $user->id, 'currency_id' => $usdCurrency->id]);
        $eurAccount = Account::factory()->create(['user_id' => $user->id, 'currency_id' => $eurCurrency->id]);
        $gbpAccount = Account::factory()->create(['user_id' => $user->id, 'currency_id' => $gbpCurrency->id]);

        $transactions = [
            // USD transactions
            ['account_id' => $usdAccount->id, 'amount' => 50, 'type' => 'income'],   // $50.00
            ['account_id' => $usdAccount->id, 'amount' => 10, 'type' => 'expense'],  // $10.00

            // EUR transactions
            ['account_id' => $eurAccount->id, 'amount' => 70, 'type' => 'income'],   // €70.00
            ['account_id' => $eurAccount->id, 'amount' => 20, 'type' => 'expense'],  // €20.00

            // GBP transactions
            ['account_id' => $gbpAccount->id, 'amount' => 30, 'type' => 'income'],   // £30.00
            ['account_id' => $gbpAccount->id, 'amount' => 10, 'type' => 'expense'],  // £10.00
        ];

        foreach ($transactions as $transaction) {
            Transaction::factory()->create($transaction);
        }

        $result = $this->transactionRepository->getFilteredTransactionsInfoForUser($user->id);

        $this->assertCount(3, $result);

        $usdResult = collect($result)->firstWhere('currency', 'USD');
        $this->assertEquals(10, $usdResult['total_expense']);
        $this->assertEquals(50, $usdResult['total_income']);
        $this->assertEquals(10, $usdResult['min_transaction']);
        $this->assertEquals(50, $usdResult['max_transaction']);

        $eurResult = collect($result)->firstWhere('currency', 'EUR');
        $this->assertEquals(20, $eurResult['total_expense']);
        $this->assertEquals(70, $eurResult['total_income']);
        $this->assertEquals(20, $eurResult['min_transaction']);
        $this->assertEquals(70, $eurResult['max_transaction']);

        $gbpResult = collect($result)->firstWhere('currency', 'GBP');
        $this->assertEquals(10, $gbpResult['total_expense']);
        $this->assertEquals(30, $gbpResult['total_income']);
        $this->assertEquals(10, $gbpResult['min_transaction']);
        $this->assertEquals(30, $gbpResult['max_transaction']);
    }
}
