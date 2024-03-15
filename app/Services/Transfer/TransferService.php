<?php

namespace App\Services\Transfer;

use App\Data\Transfer\TransferData;
use App\Data\Transfer\TransferUpdateData;
use App\Models\Account\Account;
use App\Models\Transfer\Transfer;
use App\Services\Account\AccountService;
use Illuminate\Support\Collection;

final readonly class TransferService
{
    public function __construct(
        private readonly AccountService $accountService
    ) {
    }

    public function getAccountTransfers(Account $account): Collection
    {
        return $account->transfers;
    }

    public function getAccountTransfersPaginated(Account $account): Collection
    {
        return $account->transfers->paginate();
    }

    public function createTransfer(Account $accountFrom, TransferData $data): Transfer
    {
        return \DB::transaction(function () use ($accountFrom, $data) {
            $accountTo = Account::findOrFail($data->account_to_id);
            $userId = $accountFrom->user_id;

            if ($userId !== $accountTo->user_id) {
                throw new \Exception('You can only transfer to your own accounts');
            }

            $this->accountService->increaseAccountBalance($accountFrom, $data->amount);
            $this->accountService->decreaseAccountBalance($accountTo, $data->amount);

            $transferFrom = $accountFrom->transferFrom()->create([
                'account_to_id' => $accountTo->id,
                'amount' => $data->amount,
                'comment' => $data->comment,
            ]);

            $accountTo->transferTo()->create([
                'account_from_id' => $accountFrom->id,
                'amount' => $data->amount,
                'comment' => $data->comment,
            ]);

            return $transferFrom;
        });
    }

    public function updateTransfer(Transfer $transfer, TransferUpdateData $data): Transfer
    {
        return \DB::transaction(function () use ($transfer, $data) {
            $oldTransferFrom = $transfer->accountFrom;
            $oldTransferTo = $transfer->accountTo;

            $this->accountService->increaseAccountBalance($oldTransferFrom, $transfer->amount);
            $this->accountService->decreaseAccountBalance($oldTransferTo, $transfer->amount);

            $newTransferFrom = Account::findOrFail($data->account_from_id);
            $newTransferTo = Account::findOrFail($data->account_to_id);

            $this->accountService->decreaseAccountBalance($newTransferFrom, $data->amount);
            $this->accountService->increaseAccountBalance($newTransferTo, $data->amount);

            $transfer->update([
                'account_from_id' => $newTransferFrom->id,
                'account_to_id' => $newTransferTo->id,
                'amount' => $data->amount,
                'comment' => $data->comment,
            ]);
        });
    }

    public function deleteTransfer(Transfer $transfer): void
    {
        \DB::transaction(function () use ($transfer) {
            $transferFrom = $transfer->accountFrom;
            $transferTo = $transfer->accountTo;

            $this->accountService->increaseAccountBalance($transferFrom, $transfer->amount);
            $this->accountService->decreaseAccountBalance($transferTo, $transfer->amount);

            $transfer->delete();
        });
    }
}
