<?php

namespace App\Http\Controllers\api\v1\User;

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

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    public function index(IndexRequest $request): UserCollection
    {
        return new UserCollection($this->userService->indexPaginated($request->per_page ?? 10));
    }

    public function store(UserCreateRequest $request): UserResource
    {
        $data = UserCreateData::from($request);

        return new UserResource($this->userService->store($data));
    }

    public function show(User $user): UserResource
    {
        return new UserResource($this->userService->show($user));
    }

    public function update(UserUpdateRequest $request, User $user): UserResource
    {
        $data = UserUpdateData::from($request);

        return new UserResource($this->userService->update($data, $user));
    }

    public function destroy(User $user): JsonResponse
    {
        $result = $this->userService->destroy($user);

        return response()->json([
            'success' => $result,
        ]);
    }
}
