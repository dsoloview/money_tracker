<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Data\User\Setting\UserSettingData;
use App\Data\User\UserCreateData;
use App\Data\User\UserUpdateData;
use App\Data\User\UserUpdatePasswordData;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserUpdatePasswordRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Requests\User\UserUpdateSettingsRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserResource;
use App\Http\Resources\User\UserSetting\UserSettingResource;
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
        $this->authorize('viewAny', User::class);

        return new UserCollection($this->userService->indexPaginated($request->per_page ?? 10));
    }

    #[Endpoint('Create a new user')]
    #[ResponseFromApiResource(UserResource::class, User::class)]
    public function store(UserCreateRequest $request): UserResource
    {
        $this->authorize('create', User::class);

        $data = UserCreateData::from($request);

        return new UserResource($this->userService->store($data));
    }

    #[Endpoint('Get a user by id')]
    #[ResponseFromApiResource(UserResource::class, User::class, with: ['roles', 'settings', 'settings.language', 'settings.mainCurrency'])]
    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);

        return new UserResource($this->userService->show($user));
    }

    #[Endpoint('Update a user')]
    #[ResponseFromApiResource(UserResource::class, User::class)]
    public function update(UserUpdateRequest $request, User $user): UserResource
    {
        $this->authorize('update', $user);

        $data = UserUpdateData::from($request);

        return new UserResource($this->userService->update($data, $user)->load('roles', 'settings', 'settings.language', 'settings.mainCurrency'));
    }

    public function updateSettings(UserUpdateSettingsRequest $request, User $user): UserSettingResource
    {
        $this->authorize('update', $user);

        $data = UserSettingData::from($request);

        $updatedSettings = $this->userService->updateSettings($data, $user)->load('language', 'mainCurrency');

        return new UserSettingResource($updatedSettings);
    }

    public function updatePassword(UserUpdatePasswordRequest $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $data = UserUpdatePasswordData::from($request);

        $success = $this->userService->updatePassword($data, $user);

        return response()->json([
            'success' => $success,
        ]);
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
