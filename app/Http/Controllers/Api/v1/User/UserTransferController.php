<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Transfer\TransferCollection;
use App\Models\Transfer\Transfer;
use App\Models\User;
use App\Services\User\Transfer\UserTransferService;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'User transfers', description: 'User transfers')]
#[Authenticated]
class UserTransferController extends Controller
{
    public function __construct(
        private readonly UserTransferService $userTransactionService
    ) {
    }

    #[Endpoint('Get all user transactions')]
    #[ResponseFromApiResource(
        TransferCollection::class,
        Transfer::class,
        with: ['accountFrom', 'accountFrom.currency', 'accountTo', 'accountTo.currency'],
        paginate: 10)
    ]
    public function index(User $user)
    {
        $this->authorize('view', $user);

        $transfers = $this->userTransactionService->getUserTransfersPaginated($user);

        return new TransferCollection($transfers);
    }
}
