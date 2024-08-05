<?php

namespace Tests\Unit\Services\Account;

use App\Data\Account\AccountData;
use App\Models\Account\Account;
use App\Models\Currency\Currency;
use App\Models\User;
use App\Services\Account\AccountService;
use Illuminate\Support\Collection;
use Tests\SeededTestCase;

class AccountServiceTest extends SeededTestCase
{
    private AccountService $accountService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->accountService = app(AccountService::class);
    }

    public function testGetUserAccounts()
    {
        $user = User::factory()->hasAccounts(3)->create();

        $accounts = $this->accountService->getUserAccounts($user);

        $this->assertInstanceOf(Collection::class, $accounts);
        $this->assertCount(3, $accounts);
    }

    public function testGetUserAccountsWithRelationships()
    {
        $usdCurrency = Currency::where('code', 'USD')->first();
        $user = User::factory()
            ->hasAccounts(2, [
                'currency_id' => $usdCurrency->id,
            ])
            ->create();

        $accounts = $this->accountService->getUserAccounts($user);
        
        $this->assertTrue($accounts->first()->relationLoaded('currency'));
    }

    public function testGetAccountByNameUserAndCurrencyCode()
    {
        $usdCurrency = Currency::where('code', 'USD')->first();
        $eurCurrency = Currency::where('code', 'EUR')->first();
        $user = User::factory()->create();
        $account1 = Account::factory()->create([
            'name' => 'Account',
            'currency_id' => $usdCurrency->id,
        ]);
        $account2 = Account::factory()->create([
            'name' => 'Account 1',
            'currency_id' => $usdCurrency->id,
        ]);
        $account3 = Account::factory()->create([
            'name' => 'Account',
            'currency_id' => $eurCurrency->id,
        ]);

        $user->accounts()->saveMany([$account1, $account2, $account3]);

        $account = $this->accountService->getAccountByNameUserAndCurrencyCode('Account', $user, $usdCurrency->code);

        $this->assertEquals($account1->id, $account->id);
    }

    public function testGetAccountByNameUserAndCurrencyCodeWithInvalidName()
    {
        $usdCurrency = Currency::where('code', 'USD')->first();
        $user = User::factory()->create();
        Account::factory()->create([
            'name' => 'ValidAccount',
            'currency_id' => $usdCurrency->id,
            'user_id' => $user->id,
        ]);

        $account = $this->accountService->getAccountByNameUserAndCurrencyCode('InvalidAccount', $user,
            $usdCurrency->code);

        $this->assertNull($account, 'Expected no account to be found with an invalid name.');
    }

    public function testGetAccountByNameUserAndCurrencyCodeWithInvalidCurrency()
    {
        $usdCurrency = Currency::where('code', 'USD')->first();
        $eurCurrency = Currency::where('code', 'EUR')->first();
        $user = User::factory()->create();
        Account::factory()->create([
            'name' => 'Account',
            'currency_id' => $usdCurrency->id,
            'user_id' => $user->id,
        ]);

        $account = $this->accountService->getAccountByNameUserAndCurrencyCode('Account', $user, $eurCurrency->code);

        $this->assertNull($account, 'Expected no account to be found with an invalid currency code.');
    }

    public function testSaveAccountForUser()
    {
        $usdCurrency = Currency::where('code', 'USD')->first();
        $user = User::factory()->create();

        $accountData = new AccountData(
            currency_id: $usdCurrency->id,
            balance: 100,
            name: 'Account',
            bank: 'Bank',
        );

        $account = $this->accountService->saveAccountForUser($user, $accountData);

        $this->assertEquals($account->only(['currency_id', 'balance', 'name', 'bank']), $accountData->all());
        $this->assertDatabaseHas('accounts', [
            'user_id' => $user->id,
            'currency_id' => $usdCurrency->id,
            'balance' => 10000,
            'name' => 'Account',
            'bank' => 'Bank',
        ]);
    }

    public function testUpdateAccount()
    {
        $account = Account::factory()->create();
        $accountData = new AccountData(
            currency_id: $account->currency_id,
            balance: 200,
            name: 'Account 1',
            bank: 'Bank 1',
        );

        $updatedAccount = $this->accountService->updateAccount($account, $accountData);

        $this->assertEquals($updatedAccount->only(['currency_id', 'balance', 'name', 'bank']), $accountData->all());
    }

    public function testIncreaseAccountBalance()
    {
        $account = Account::factory()->create(['balance' => 100]);
        $amount = 50;

        $updatedAccount = $this->accountService->increaseAccountBalance($account, $amount);

        $this->assertEquals(150, $updatedAccount->balance);
    }

    public function testDecreaseAccountBalance()
    {
        $account = Account::factory()->create(['balance' => 100]);
        $amount = 50;

        $updatedAccount = $this->accountService->decreaseAccountBalance($account, $amount);

        $this->assertEquals(50, $updatedAccount->balance);
    }
}
