<?php

namespace App\Http\Controllers\Api\v1\Account;

use App\Data\Account\AccountData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\AccountRequest;
use App\Http\Resources\Account\AccountCollection;
use App\Http\Resources\Account\AccountResource;
use App\Http\Resources\Currency\CurrencyResource;
use App\Models\Account\Account;
use App\Models\User;
use App\Services\Account\AccountBalanceService;
use App\Services\Account\AccountService;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'Account', description: 'Account management')]
#[Authenticated]
class AccountController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
        private readonly AccountBalanceService $accountBalanceService
    ) {
    }

    #[Endpoint('Get all accounts for a user')]
    #[ResponseFromApiResource(AccountCollection::class, Account::class, with: ['currency'])]
    public function index(User $user): AccountCollection
    {
        $this->authorize('viewAny', [Account::class, $user]);
        $userAccounts = $this->accountService->getUserAccounts($user);

        return new AccountCollection($userAccounts);
    }

    #[Endpoint('Create a new account for a user')]
    #[ResponseFromApiResource(AccountResource::class, Account::class, with: ['currency'])]
    public function store(User $user, AccountRequest $request)
    {
        $this->authorize('create', [Account::class, $user]);
        $data = AccountData::from($request);
        $account = $this->accountService->saveAccountForUser($user, $data);

        return new AccountResource($account);

    }

    #[Endpoint('Get a single account')]
    #[ResponseFromApiResource(AccountResource::class, Account::class, with: ['currency'])]
    public function show(Account $account)
    {
        $this->authorize('view', $account);

        return new AccountResource($account->load('currency'));
    }

    #[Endpoint('Update a single account')]
    #[ResponseFromApiResource(AccountResource::class, Account::class, with: ['currency'])]
    public function update(AccountRequest $request, Account $account)
    {

        $this->authorize('update', $account);
        $data = AccountData::from($request);
        $account = $this->accountService->updateAccount($account, $data);

        return new AccountResource($account);
    }

    #[Endpoint('Delete a single account')]
    #[Response(['message' => 'Account deleted successfully'])]
    public function destroy(Account $account)
    {
        $this->authorize('delete', $account);
        $account->delete();

        return response()->json([
            'message' => 'Account deleted successfully',
        ]);
    }

    #[Endpoint('Get balance for a user')]
    #[Response([
        'data' => [
            'balance' => 1000.00,
            'currency' => [
                'id' => 1,
                'code' => 'USD',
                'name' => 'United States Dollar',
                'symbol' => '$',
            ],
        ],
    ])]
    public function balance(User $user)
    {
        $this->authorize('viewAny', [Account::class, $user]);
        $balance = $this->accountBalanceService->getUserAccountsBalance($user);

        return response()->json([
            'data' => [
                'balance' => $balance,
                'currency' => new CurrencyResource($user->currency),
            ],
        ]);
    }
}
