<?php

namespace App\Services\User\Transfer;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class UserTransferService
{
    public function getUserTransfersPaginated(User $user): LengthAwarePaginator
    {
        return $user
            ->transfers()
            ->filter()
            ->sort()
            ->with(['accountTo', 'accountTo.currency', 'accountFrom', 'accountFrom.currency'])
            ->paginate(10);
    }

    public function getUserTransfersFilteredByDates(User $user, Carbon $fromDate, Carbon $toDate): Collection
    {
        return $user
            ->transfers()
            ->whereDate('date', '>=', $fromDate)
            ->whereDate('date', '<=', $toDate)
            ->with(['accountTo', 'accountTo.currency', 'accountFrom', 'accountFrom.currency'])
            ->get();
    }
}
