<?php

namespace App\Services\Transfer;

use App\Data\Transfer\TransferData;
use App\Data\Transfer\TransferUpdateData;
use App\Models\Account\Account;
use App\Models\Transfer\Transfer;
use App\Services\Account\AccountService;
use Illuminate\Pagination\LengthAwarePaginator;
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

    public function getAccountTransfersPaginated(Account $account): LengthAwarePaginator
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

            $this->accountService->increaseAccountBalance($accountTo, $data->amount_to);
            $this->accountService->decreaseAccountBalance($accountFrom, $data->amount_from);

            return $accountFrom->transferFrom()->create([
                'account_to_id' => $accountTo->id,
                'amount_from' => $data->amount_from,
                'amount_to' => $data->amount_to,
                'date' => $data->date,
                'comment' => $data->comment,
            ]);
        });
    }

    public function updateTransfer(Transfer $transfer, TransferUpdateData $data): Transfer
    {
        return \DB::transaction(function () use ($transfer, $data) {
            $oldTransferFrom = $transfer->accountFrom;
            $oldTransferTo = $transfer->accountTo;

            $this->accountService->increaseAccountBalance($oldTransferFrom, $transfer->amount_from);
            $this->accountService->decreaseAccountBalance($oldTransferTo, $transfer->amount_to);

            $newTransferFrom = Account::findOrFail($data->account_from_id);
            $newTransferTo = Account::findOrFail($data->account_to_id);

            $this->accountService->decreaseAccountBalance($newTransferFrom, $data->amount_from);
            $this->accountService->increaseAccountBalance($newTransferTo, $data->amount_to);

            $transfer->update([
                'account_from_id' => $newTransferFrom->id,
                'account_to_id' => $newTransferTo->id,
                'amount_from' => $data->amount_from,
                'amount_to' => $data->amount_to,
                'comment' => $data->comment,
            ]);

            return $transfer;
        });
    }

    public function deleteTransfer(Transfer $transfer): void
    {
        \DB::transaction(function () use ($transfer) {
            $transferFrom = $transfer->accountFrom;
            $transferTo = $transfer->accountTo;

            $this->accountService->increaseAccountBalance($transferFrom, $transfer->amount_from);
            $this->accountService->decreaseAccountBalance($transferTo, $transfer->amount_to);

            $transfer->delete();
        });
    }
}
