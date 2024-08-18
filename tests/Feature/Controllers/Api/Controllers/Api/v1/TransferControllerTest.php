<?php

namespace Tests\Feature\Controllers\Api\Controllers\Api\v1;

use App\Models\Account\Account;
use App\Models\Transfer\Transfer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\WithTestUser;

class TransferControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithTestUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTestUser();
    }

    public function testGetAllTransfersForAccount()
    {
        Sanctum::actingAs($this->user);

        $account = Account::factory()->for($this->user)->createOne();
        Transfer::factory()->count(5)->create([
            'account_from_id' => $account->id,
        ]);

        $response = $this->getJson(route('accounts.transfers.index', $account));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'comment',
                    'amount_from',
                    'amount_to',
                    'date',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function testNotShowAlienAccountTransfers()
    {
        Sanctum::actingAs($this->user);

        $account = Account::factory()->createOne();
        Transfer::factory()->count(5)->create([
            'account_from_id' => $account->id,
        ]);

        $response = $this->getJson(route('accounts.transfers.index', $account));

        $response->assertForbidden();
    }

    public function testStoreTransfer()
    {
        Sanctum::actingAs($this->user);
        $accountFrom = Account::factory()->for($this->user)->createOne();
        $accountTo = Account::factory()->for($this->user)->createOne();

        // 'account_to_id' => ['required', 'exists:accounts,id'],
        //            'comment' => ['nullable', 'string', 'max:255'],
        //            'amount_to' => ['numeric', 'required'],
        //            'amount_from' => ['numeric', 'required'],
        //            'date' => ['required', 'date'],

        $response = $this->postJson(route('accounts.transfers.store', $accountFrom), [
            'account_to_id' => $accountTo->id,
            'comment' => 'Test transfer',
            'amount_to' => 100,
            'amount_from' => 100,
            'date' => now()->toDateString(),
        ]);

        $response->assertCreated();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'comment',
                'amount_from',
                'amount_to',
                'date',
                'created_at',
                'updated_at',
            ],
        ]);

        $this->assertDatabaseHas(Transfer::class, [
            'account_from_id' => $accountFrom->id,
            'account_to_id' => $accountTo->id,
            'comment' => 'Test transfer',
            'amount_from' => 10000,
            'amount_to' => 10000,
        ]);
    }

    public function testNotStoreTransferToAlienAccountTo()
    {
        Sanctum::actingAs($this->user);
        $accountFrom = Account::factory()->for($this->user)->createOne();
        $accountTo = Account::factory()->createOne();

        $response = $this->postJson(route('accounts.transfers.store', $accountFrom), [
            'account_to_id' => $accountTo->id,
            'comment' => 'Test transfer',
            'amount_to' => 100,
            'amount_from' => 100,
            'date' => now()->toDateString(),
        ]);

        $response->assertForbidden();
    }

    public function testNotStoreTransferForAlienAccountFrom()
    {
        Sanctum::actingAs($this->user);
        $accountFrom = Account::factory()->createOne();
        $accountTo = Account::factory()->for($this->user)->createOne();

        $response = $this->postJson(route('accounts.transfers.store', $accountFrom), [
            'account_to_id' => $accountTo->id,
            'comment' => 'Test transfer',
            'amount_to' => 100,
            'amount_from' => 100,
            'date' => now()->toDateString(),
        ]);

        $response->assertForbidden();
    }

    public function testShowTransfer()
    {
        Sanctum::actingAs($this->user);
        $accountFrom = Account::factory()->for($this->user)->createOne();
        $accountTo = Account::factory()->for($this->user)->createOne();

        $transfer = Transfer::factory()->createOne([
            'account_from_id' => $accountFrom->id,
            'account_to_id' => $accountTo->id,
        ]);

        $response = $this->getJson(route('transfers.show', $transfer));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'comment',
                'amount_from',
                'amount_to',
                'account_from',
                'account_to',
                'date',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function testNotShowAlienTransfer()
    {
        Sanctum::actingAs($this->user);
        $accountFrom = Account::factory()->createOne();
        $accountTo = Account::factory()->createOne();

        $transfer = Transfer::factory()->createOne([
            'account_from_id' => $accountFrom->id,
            'account_to_id' => $accountTo->id,
        ]);

        $response = $this->getJson(route('transfers.show', $transfer));

        $response->assertForbidden();
    }

    public function testUpdateTransfer()
    {
        Sanctum::actingAs($this->user);
        $accountFrom = Account::factory()->for($this->user)->createOne();
        $accountTo = Account::factory()->for($this->user)->createOne();
        $newAccountTo = Account::factory()->for($this->user)->createOne();

        $transfer = Transfer::factory()->createOne([
            'account_from_id' => $accountFrom->id,
            'account_to_id' => $accountTo->id,
        ]);

        $response = $this->putJson(route('transfers.update', $transfer), [
            'account_from_id' => $accountFrom->id,
            'account_to_id' => $newAccountTo->id,
            'comment' => 'Test transfer',
            'amount_to' => 100,
            'amount_from' => 100,
            'date' => now()->toDateString(),
        ]);


        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'comment',
                'amount_from',
                'amount_to',
                'date',
                'created_at',
                'updated_at',
            ],
        ]);

        $this->assertDatabaseHas(Transfer::class, [
            'id' => $transfer->id,
            'account_from_id' => $accountFrom->id,
            'account_to_id' => $newAccountTo->id,
            'comment' => 'Test transfer',
            'amount_from' => 10000,
            'amount_to' => 10000,
        ]);
    }

    public function testNotUpdateAlienTransfer()
    {
        Sanctum::actingAs($this->user);
        $accountFrom = Account::factory()->createOne();
        $accountTo = Account::factory()->createOne();
        $newAccountTo = Account::factory()->createOne();

        $transfer = Transfer::factory()->createOne([
            'account_from_id' => $accountFrom->id,
            'account_to_id' => $accountTo->id,
        ]);

        $response = $this->putJson(route('transfers.update', $transfer), [
            'account_from_id' => $accountFrom->id,
            'account_to_id' => $newAccountTo->id,
            'comment' => 'Test transfer',
            'amount_to' => 100,
            'amount_from' => 100,
            'date' => now()->toDateString(),
        ]);

        $response->assertForbidden();
    }

    public function testDeleteTransfer()
    {
        Sanctum::actingAs($this->user);
        $accountFrom = Account::factory()->for($this->user)->createOne();
        $accountTo = Account::factory()->for($this->user)->createOne();

        $transfer = Transfer::factory()->createOne([
            'account_from_id' => $accountFrom->id,
            'account_to_id' => $accountTo->id,
        ]);

        $response = $this->deleteJson(route('transfers.destroy', $transfer));

        $response->assertOk();
        $response->assertJsonStructure([
            'message',
        ]);

        $this->assertDatabaseMissing(Transfer::class, [
            'id' => $transfer->id,
        ]);
    }

    public function testNotDeleteAlienTransfer()
    {
        Sanctum::actingAs($this->user);
        $accountFrom = Account::factory()->createOne();
        $accountTo = Account::factory()->createOne();

        $transfer = Transfer::factory()->createOne([
            'account_from_id' => $accountFrom->id,
            'account_to_id' => $accountTo->id,
        ]);

        $response = $this->deleteJson(route('transfers.destroy', $transfer));

        $response->assertForbidden();
    }
}
