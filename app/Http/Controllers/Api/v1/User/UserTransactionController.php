<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\Currency\CurrencyResource;
use App\Http\Resources\Transaction\TransactionCollection;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Services\User\Transaction\UserTransactionService;
use Illuminate\Http\JsonResponse;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'User transactions', description: 'User transactions')]
#[Authenticated]
class UserTransactionController extends Controller
{
    public function __construct(
        private readonly UserTransactionService $userTransactionService
    ) {
    }

    #[Endpoint('Get all user transactions')]
    #[ResponseFromApiResource(TransactionCollection::class, Transaction::class, with: ['account'], paginate: 10, additional: ['info' => ['test']])]
    public function index(User $user)
    {
        $this->authorize('view', $user);

        $transactions = $this->userTransactionService->getUserTransactionsPaginated($user);
        $transactionsInfo = $this->userTransactionService->getTransactionsInfoForUser($user);

        return (new TransactionCollection($transactions))->additional(['info' => $transactionsInfo]);
    }

    #[Endpoint('Get min and max transaction amounts')]
    #[Response([
        'data' => [
            'min' => 0,
            'max' => 0,
        ],
    ]
    )]
    public function minMax(User $user)
    {
        $this->authorize('view', $user);

        return new JsonResponse([
            'data' => [
                'min' => $this->userTransactionService->getMinTransactionAmount($user),
                'max' => $this->userTransactionService->getMaxTransactionAmount($user),
            ],
        ]);
    }

    #[Endpoint('Get transactions info')]
    #[ResponseFromApiResource(CurrencyResource::class, additional: [
        'total_expense' => 0,
        'total_income' => 0,
        'min_transaction' => 0,
        'max_transaction' => 0,
    ])]
    public function transactionsInfo(User $user)
    {
        $this->authorize('view', $user);

        return new JsonResponse([
            'data' => $this->userTransactionService->getTransactionsInfoForUser($user),
        ]);
    }
}
