<?php

namespace App\Http\Controllers\api\v1\User;

use App\Data\User\UserCreateData;
use App\Data\User\UserUpdateData;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Interfaces\Services\IUserService;
use App\Models\User;

class UserController extends Controller
{
    public function __construct(
        private readonly IUserService $userService
    ) {
    }

    public function index()
    {
        return new UserCollection($this->userService->index());
    }

    public function store(UserCreateRequest $request)
    {
        $data = UserCreateData::from($request);

        return new UserResource($this->userService->store($data));
    }

    public function show(User $user)
    {
        return new UserResource($this->userService->show($user));
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $data = UserUpdateData::from($request);

        return new UserResource($this->userService->update($data, $user));
    }

    public function destroy(User $user)
    {
        return $this->userService->destroy($user);
    }
}
