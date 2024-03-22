<?php

namespace App\Services\User\Transfer;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

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

}
