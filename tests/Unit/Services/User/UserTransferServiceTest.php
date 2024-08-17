<?php

namespace Tests\Unit\Services\User;

use App\Models\Account\Account;
use App\Models\Transfer\Transfer;
use App\Models\User;
use App\Services\User\Transfer\UserTransferService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\SeededTestCase;

class UserTransferServiceTest extends SeededTestCase
{
    use RefreshDatabase;

    private UserTransferService $userTransferService;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userTransferService = app(UserTransferService::class);
        $this->user = User::factory()->createOne();
    }

    public function testGetUserTransfersPaginated()
    {
        $account = Account::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        Transfer::factory()->count(15)->create([
            'account_from_id' => $account->id,
        ]);

        $result = $this->userTransferService->getUserTransfersPaginated($this->user);

        $this->assertCount(10, $result->items());
        $this->assertEquals(15, $result->total());
    }

    public function testGetUserTransfersFilteredByDates()
    {
        $account = Account::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        $startDate = Carbon::parse('2023-01-01');
        $endDate = Carbon::parse('2023-01-31');

        Transfer::factory()->create([
            'account_from_id' => $account->id,
            'date' => '2023-01-05',
        ]);

        Transfer::factory()->create([
            'account_from_id' => $account->id,
            'date' => '2023-02-01',
        ]);

        $result = $this->userTransferService->getUserTransfersFilteredByDates($this->user, $startDate, $endDate);

        $this->assertCount(1, $result);
        $this->assertEquals('2023-01-05', $result->first()->date->toDateString());
    }

    public function testGetUserTransfersFilteredByDatesWithMultipleTransfers()
    {
        $account = Account::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        $startDate = Carbon::parse('2023-01-01');
        $endDate = Carbon::parse('2023-01-31');

        Transfer::factory()->create([
            'account_from_id' => $account->id,
            'date' => '2023-01-05',
        ]);

        Transfer::factory()->create([
            'account_from_id' => $account->id,
            'date' => '2023-01-15',
        ]);

        Transfer::factory()->create([
            'account_from_id' => $account->id,
            'date' => '2023-01-25',
        ]);

        Transfer::factory()->create([
            'account_from_id' => $account->id,
            'date' => '2023-02-01',
        ]);

        $result = $this->userTransferService->getUserTransfersFilteredByDates($this->user, $startDate, $endDate);

        $this->assertCount(3, $result);
        $this->assertEquals([Carbon::parse('2023-01-05'), Carbon::parse('2023-01-15'), Carbon::parse('2023-01-25')],
            $result->pluck('date')->toArray());
    }

    public function testGetUserTransfersPaginatedWithNoTransfers()
    {
        $result = $this->userTransferService->getUserTransfersPaginated($this->user);

        $this->assertCount(0, $result->items());
        $this->assertEquals(0, $result->total());
    }
}
