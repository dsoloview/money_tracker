<?php

namespace Tests\Unit\Services\Transfer;

use App\Data\Transfer\TransferData;
use App\Data\Transfer\TransferUpdateData;
use App\Models\Account\Account;
use App\Models\Transfer\Transfer;
use App\Models\User;
use App\Services\Transfer\TransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Tests\TestCase;

class TransferServiceTest extends TestCase
{
    use RefreshDatabase;

    private TransferService $transferService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transferService = app(TransferService::class);
    }

    public function testGetAccountTransfers()
    {
        $accountFrom = Account::factory()->create();
        $accountTo = Account::factory()->create();

        $transfer = Transfer::factory()->count(3)->create([
            'account_from_id' => $accountFrom->id,
            'account_to_id' => $accountTo->id,
        ]);

        $result = $this->transferService->getAccountTransfers($accountFrom);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertContainsOnlyInstancesOf(Transfer::class, $result);
        $this->assertCount(3, $result);
    }

    public function testGetAccountTransfersPaginated()
    {
        $accountFrom = Account::factory()->create();
        $accountTo = Account::factory()->create();

        Transfer::factory()->count(15)->create([
            'account_from_id' => $accountFrom->id,
            'account_to_id' => $accountTo->id,
        ]);

        $result = $this->transferService->getAccountTransfersPaginated($accountFrom);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(10, $result->items());
    }

    public function testCreateTransfer()
    {
        $user = User::factory()->create();
        $accountFrom = Account::factory()->create(['balance' => 1000, 'user_id' => $user->id]);
        $accountTo = Account::factory()->create(['balance' => 500, 'user_id' => $user->id]);

        $data = new TransferData(
            account_to_id: $accountTo->id,
            comment: 'Test transfer',
            amount_from: 200,
            amount_to: 200,
            date: now()
        );

        $transfer = $this->transferService->createTransfer($accountFrom, $data);

        $this->assertInstanceOf(Transfer::class, $transfer);
        $this->assertEquals($accountTo->id, $transfer->account_to_id);
        $this->assertEquals(200, $transfer->amount_from);
        $this->assertEquals(200, $transfer->amount_to);
        $this->assertEquals('Test transfer', $transfer->comment);

        $accountFrom->refresh();
        $accountTo->refresh();
        $this->assertEquals(800, $accountFrom->balance);
        $this->assertEquals(700, $accountTo->balance);
    }

    public function testCreateTransferThrowsExceptionForDifferentUserAccounts()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $accountFrom = Account::factory()->create(['user_id' => $user1->id, 'balance' => 1000]);
        $accountTo = Account::factory()->create(['user_id' => $user2->id, 'balance' => 500]);

        $data = new TransferData(
            account_to_id: $accountTo->id,
            comment: 'Test transfer',
            amount_from: 200,
            amount_to: 200,
            date: now()
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('You can only transfer to your own accounts');

        $this->transferService->createTransfer($accountFrom, $data);
    }

    public function testUpdateTransfer()
    {
        $user = User::factory()->create();
        $accountFromOld = Account::factory()->create(['balance' => 1000, 'user_id' => $user->id]);
        $accountToOld = Account::factory()->create(['balance' => 500, 'user_id' => $user->id]);

        $transfer = Transfer::factory()->create([
            'account_from_id' => $accountFromOld->id,
            'account_to_id' => $accountToOld->id,
            'amount_from' => 300,
            'amount_to' => 300,
        ]);

        $accountFromNew = Account::factory()->create(['balance' => 1200, 'user_id' => $user->id]);
        $accountToNew = Account::factory()->create(['balance' => 600, 'user_id' => $user->id]);

        $data = new TransferUpdateData(
            account_from_id: $accountFromNew->id,
            account_to_id: $accountToNew->id,
            comment: 'Updated transfer',
            amount_from: 150,
            amount_to: 150
        );

        $updatedTransfer = $this->transferService->updateTransfer($transfer, $data);

        $this->assertInstanceOf(Transfer::class, $updatedTransfer);
        $this->assertEquals($accountFromNew->id, $updatedTransfer->account_from_id);
        $this->assertEquals($accountToNew->id, $updatedTransfer->account_to_id);
        $this->assertEquals(150, $updatedTransfer->amount_from);
        $this->assertEquals(150, $updatedTransfer->amount_to);
        $this->assertEquals('Updated transfer', $updatedTransfer->comment);

        $accountFromOld->refresh();
        $accountToOld->refresh();
        $accountFromNew->refresh();
        $accountToNew->refresh();

        $this->assertEquals(1300, $accountFromOld->balance);
        $this->assertEquals(200, $accountToOld->balance);

        $this->assertEquals(1050, $accountFromNew->balance);
        $this->assertEquals(750, $accountToNew->balance);
    }

    public function testDeleteTransfer()
    {
        $user = User::factory()->create();
        $accountFrom = Account::factory()->create(['balance' => 1000, 'user_id' => $user->id]);
        $accountTo = Account::factory()->create(['balance' => 500, 'user_id' => $user->id]);

        $transfer = Transfer::factory()->create([
            'account_from_id' => $accountFrom->id,
            'account_to_id' => $accountTo->id,
            'amount_from' => 300,
            'amount_to' => 300,
        ]);

        $this->transferService->deleteTransfer($transfer);

        $this->assertModelMissing($transfer);

        $accountFrom->refresh();
        $accountTo->refresh();
        
        $this->assertEquals(1300, $accountFrom->balance);
        $this->assertEquals(200, $accountTo->balance);
    }
}
