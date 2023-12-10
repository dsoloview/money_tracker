<?php

namespace App\Http\Controllers\Api\v1\Transaction;

use App\Data\Transaction\TransactionData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\TransactionRequest;
use App\Http\Resources\Transaction\TransactionCollection;
use App\Http\Resources\Transaction\TransactionResource;
use App\Models\Account\Account;
use App\Models\Transaction\Transaction;
use App\Services\Transaction\TransactionService;
use Illuminate\Http\JsonResponse;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group('Transactions')]
#[Authenticated]
class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionService $transactionService,
    ) {
    }

    #[Endpoint('Get all transactions for an account')]
    #[ResponseFromApiResource(TransactionCollection::class, Transaction::class, with: ['categories'], paginate: 10)]
    public function index(Account $account): TransactionCollection
    {
        $accountTransactions = $this->transactionService->getAccountTransactionsPaginated($account);

        return new TransactionCollection($accountTransactions);
    }

    #[Endpoint('Create a new transaction for an account')]
    #[ResponseFromApiResource(TransactionResource::class, Transaction::class, with: ['categories'])]
    public function store(Account $account, TransactionRequest $request): TransactionResource
    {
        $data = TransactionData::from($request);
        $transaction = $this->transactionService->createTransactionForAccount($account, $data);

        return new TransactionResource($transaction);
    }

    #[Endpoint('Get a transaction')]
    #[ResponseFromApiResource(TransactionResource::class, Transaction::class, with: ['account', 'categories'])]
    public function show(Transaction $transaction): TransactionResource
    {
        return new TransactionResource($transaction->load('account', 'categories'));
    }

    #[Endpoint('Update a transaction')]
    #[ResponseFromApiResource(TransactionResource::class, Transaction::class, with: ['categories'])]
    public function update(TransactionRequest $request, Transaction $transaction): TransactionResource
    {
        $data = TransactionData::from($request);
        $transaction = $this->transactionService->updateTransaction($transaction, $data);

        return new TransactionResource($transaction);
    }

    #[Endpoint('Delete a transaction')]
    #[Response(['message' => 'Transaction deleted successfully'])]
    public function destroy(Transaction $transaction): JsonResponse
    {
        $this->transactionService->deleteTransaction($transaction);

        return response()->json([
            'message' => 'Transaction deleted successfully',
        ]);
    }
}
