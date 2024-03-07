<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Transaction\TransactionCollection;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Services\User\Transaction\UserTransactionService;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'User', description: 'User management')]
#[Authenticated]
class UserTransactionController extends Controller
{
    public function __construct(
        private readonly UserTransactionService $userTransactionService
    )
    {
    }

    #[Endpoint('Get all user transactions')]
    #[ResponseFromApiResource(TransactionCollection::class, Transaction::class, with: ['account'], paginate: 10)]
    public function index(User $user)
    {
        $transactions = $this->userTransactionService->getUserTransactionsPaginated($user);

        return new TransactionCollection($transactions);
    }
}
