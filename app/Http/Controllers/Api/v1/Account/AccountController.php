<?php

namespace App\Http\Controllers\Api\v1\Account;

use App\Data\Account\AccountData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Account\AccountRequest;
use App\Http\Resources\Account\AccountCollection;
use App\Http\Resources\Account\AccountResource;
use App\Models\Account\Account;
use App\Models\User;
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
        private readonly AccountService $accountService
    )
    {
    }

    #[Endpoint('Get all accounts for a user')]
    #[ResponseFromApiResource(AccountCollection::class, Account::class, with: ['currency'])]
    public function index(User $user): AccountCollection
    {
        $userAccounts = $this->accountService->getUserAccounts($user);

        return new AccountCollection($userAccounts);
    }

    #[Endpoint('Create a new account for a user')]
    #[ResponseFromApiResource(AccountResource::class, Account::class, with: ['currency'])]
    public function store(User $user, AccountRequest $request)
    {
        $data = AccountData::from($request);
        $account = $this->accountService->saveAccountForUser($user, $data);

        return new AccountResource($account);

    }

    #[Endpoint('Get a single account')]
    #[ResponseFromApiResource(AccountResource::class, Account::class, with: ['currency'])]
    public function show(Account $account)
    {
        return new AccountResource($account->load('currency'));
    }

    #[Endpoint('Update a single account')]
    #[ResponseFromApiResource(AccountResource::class, Account::class, with: ['currency'])]
    public function update(AccountRequest $request, Account $account)
    {
        $data = AccountData::from($request);
        $account = $this->accountService->updateAccount($account, $data);

        return new AccountResource($account);
    }

    #[Endpoint('Delete a single account')]
    #[Response(['message' => 'Account deleted successfully'])]
    public function destroy(Account $account)
    {
        $account->delete();
        return response()->json([
            'message' => 'Account deleted successfully'
        ]);
    }
}
