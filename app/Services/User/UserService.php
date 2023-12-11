<?php

namespace App\Services\User;

use App\Data\Category\CategoryData;
use App\Data\User\UserCreateData;
use App\Data\User\UserUpdateData;
use App\Enums\Role\Roles;
use App\Models\Category\Category;
use App\Models\User;
use App\Services\User\Setting\UserSettingService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

final readonly class UserService
{
    public function __construct(
        private readonly UserSettingService $userSettingService
    ) {
    }
    public function index(): Collection
    {
        return User::all();
    }

    public function indexPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return User::with('settings')->paginate($perPage);
    }

    public function store(UserCreateData $data): User
    {
        return \DB::transaction(function () use ($data) {
            $user = User::create($data->except('settings')->all());

            $user->assignRole(Roles::user->value);

            $this->userSettingService->createSettingForUser($user, $data->settings);

            return $user;
        });
    }

    public function show(User $user): User
    {
        return $user->load('roles', 'settings', 'settings.language', 'settings.mainCurrency');
    }

    public function update(UserUpdateData $data, User $user): User
    {
        $user->update($data->all());

        return $user;
    }

    public function destroy(User $user): bool
    {
        return $user->delete();
    }

    public function createUsersCategory(User $user, CategoryData $data): Category
    {
        return $user->categories()->create($data->all())->load('parentCategory');
    }
}
