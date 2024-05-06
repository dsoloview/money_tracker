<?php

namespace App\Exports;

use App\Enums\Category\CategoryTransactionType;
use App\Models\Transaction\Transaction;
use App\Models\Transfer\Transfer;
use App\Models\User;
use App\Services\User\Transaction\UserTransactionService;
use App\Services\User\Transfer\UserTransferService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MoneyTrackerExport implements FromCollection, WithHeadings, ShouldAutoSize, IExport
{
    private User $user;
    private Carbon $fromDate;
    private Carbon $toDate;
    private UserTransactionService $userTransactionService;
    private UserTransferService $userTransferService;

    public function __construct()
    {
        $this->userTransactionService = app(UserTransactionService::class);
        $this->userTransferService = app(UserTransferService::class);
    }

    public function forUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function forDates(Carbon $fromDate, Carbon $toDate): self
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;

        return $this;
    }

    public function collection(): Collection
    {
        $transactions = $this->userTransactionService
            ->getUserTransactionsFilteredByDates($this->user, $this->fromDate, $this->toDate);
        $transfers = $this->userTransferService
            ->getUserTransfersFilteredByDates($this->user, $this->fromDate, $this->toDate);

        $resultCollection = collect();
        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $resultCollection->push([
                'Date' => $transaction->date,
                'Type' => $transaction->type->value,
                'Categories' => $transaction->categories->pluck('name')->join(', '),
                'Account' => $transaction->account->name,
                'Amount' => $transaction->amount,
                'Currency' => $transaction->account->currency->code,
                'Comment' => $transaction->comment,
                'Transfer Account' => null,
                'Transfer Amount' => null,
                'Transfer Currency' => null,
            ]);
        }

        /** @var Transfer $transfer */
        foreach ($transfers as $transfer) {
            $resultCollection->push([
                'Date' => $transfer->date,
                'Type' => CategoryTransactionType::TRANSFER->value,
                'Categories' => null,
                'Account' => $transfer->accountFrom->name,
                'Amount' => $transfer->amount_from,
                'Currency' => $transfer->accountFrom->currency->code,
                'Comment' => $transfer->comment,
                'Transfer Account' => $transfer->accountTo->name,
                'Transfer Amount' => $transfer->amount_to,
                'Transfer Currency' => $transfer->accountTo->currency->code,
            ]);
        }

        $resultCollection->sort(function ($a, $b) {
            return $a['Date'] <=> $b['Date'];
        });

        return $resultCollection;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Type',
            'Categories',
            'Account',
            'Amount',
            'Currency',
            'Comment',
            'Transfer Account',
            'Transfer Amount',
            'Transfer Currency',
        ];
    }
}
