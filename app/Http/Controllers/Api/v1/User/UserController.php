<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Data\User\UserCreateData;
use App\Data\User\UserUpdateData;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'User', description: 'User management')]
#[Authenticated]
class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    #[Endpoint('Get all users')]
    #[ResponseFromApiResource(UserCollection::class, User::class, paginate: 10)]
    public function index(IndexRequest $request): UserCollection
    {
        return new UserCollection($this->userService->indexPaginated($request->per_page ?? 10));
    }

    #[Endpoint('Create a new user')]
    #[ResponseFromApiResource(UserResource::class, User::class)]
    public function store(UserCreateRequest $request): UserResource
    {
        $data = UserCreateData::from($request);

        return new UserResource($this->userService->store($data));
    }

    #[Endpoint('Get a user by id')]
    #[ResponseFromApiResource(UserResource::class, User::class)]
    public function show(User $user): UserResource
    {
        return new UserResource($this->userService->show($user));
    }

    #[Endpoint('Update a user')]
    #[ResponseFromApiResource(UserResource::class, User::class)]
    public function update(UserUpdateRequest $request, User $user): UserResource
    {
        $data = UserUpdateData::from($request);

        return new UserResource($this->userService->update($data, $user));
    }

    #[Endpoint('Delete a user')]
    #[Response(['success' => true])]
    public function destroy(User $user): JsonResponse
    {
        $result = $this->userService->destroy($user);

        return response()->json([
            'success' => $result,
        ]);
    }
}
