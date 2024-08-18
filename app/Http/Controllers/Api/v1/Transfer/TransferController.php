<?php

namespace App\Http\Controllers\Api\v1\Transfer;

use App\Data\Transfer\TransferData;
use App\Data\Transfer\TransferUpdateData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transfer\TransferCreateRequest;
use App\Http\Requests\Transfer\TransferUpdateRequest;
use App\Http\Resources\Transfer\TransferCollection;
use App\Http\Resources\Transfer\TransferResource;
use App\Models\Account\Account;
use App\Models\Transfer\Transfer;
use App\Services\Transfer\TransferService;
use Illuminate\Http\JsonResponse;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Authenticated]
#[Group('Transfers', 'Transfers between accounts')]
class TransferController extends Controller
{
    public function __construct(
        private readonly TransferService $transferService
    ) {
    }

    #[Endpoint('Get account transfers')]
    #[ResponseFromApiResource(TransferCollection::class, Transfer::class, paginate: 10)]
    public function index(Account $account): TransferCollection
    {
        $this->authorize('viewAny', [Transfer::class, $account]);
        $transfers = $this->transferService->getAccountTransfersPaginated($account);

        return new TransferCollection($transfers);
    }

    #[Endpoint('Create transfer')]
    #[ResponseFromApiResource(TransferResource::class, Transfer::class)]
    public function store(Account $account, TransferCreateRequest $request): TransferResource
    {
        $this->authorize('create', [Transfer::class, $account, $request->account_to_id]);

        $data = TransferData::from($request);
        $transferFrom = $this->transferService->createTransfer($account, $data);

        return new TransferResource($transferFrom);
    }

    #[Endpoint('Get transfer')]
    #[ResponseFromApiResource(TransferResource::class, Transfer::class)]
    public function show(Transfer $transfer): TransferResource
    {
        $this->authorize('view', $transfer);

        return new TransferResource($transfer->load('accountFrom', 'accountTo'));
    }

    #[Endpoint('Update transfer')]
    #[ResponseFromApiResource(TransferResource::class, Transfer::class)]
    public function update(Transfer $transfer, TransferUpdateRequest $request): TransferResource
    {
        $this->authorize('update', [Transfer::class, $transfer, $request->account_to_id]);
        $data = TransferUpdateData::from($request);
        $transfer = $this->transferService->updateTransfer($transfer, $data);

        return new TransferResource($transfer);
    }

    #[Endpoint('Delete transfer')]
    #[Response(['message' => 'Transfer deleted successfully'])]
    public function destroy(Transfer $transfer): JsonResponse
    {
        $this->authorize('delete', $transfer);
        $this->transferService->deleteTransfer($transfer);

        return response()->json([
            'message' => 'Transfer deleted successfully',
        ]);
    }
}
