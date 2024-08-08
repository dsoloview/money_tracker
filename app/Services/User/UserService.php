<?php

namespace App\Services\User;

use App\Data\Category\CategoryData;
use App\Data\User\Settings\UserSettingsData;
use App\Data\User\UserCreateData;
use App\Data\User\UserUpdateData;
use App\Data\User\UserUpdatePasswordData;
use App\Enums\Role\Roles;
use App\Models\Category\Category;
use App\Models\User;
use App\Models\UserSettings;
use App\Services\Category\CategoryService;
use App\Services\User\Settings\UserSettingsService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final readonly class UserService
{
    public function __construct(
        private readonly UserSettingsService $userSettingService,
        private readonly CategoryService $categoryService,
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

            $this->userSettingService->createSettingsForUser($user, $data->settings);

            $user->categories()->createMany($this->categoryService->getDefaultForLanguage($user->language)->toArray());

            return $user;
        });
    }

    public function show(User $user): User
    {
        return $user->load('roles', 'settings', 'settings.language', 'settings.mainCurrency');
    }

    public function update(UserUpdateData $data, User $user): User
    {
        return \DB::transaction(function () use ($data, $user) {
            $user->update($data->except('settings')->all());
            $user->settings->update($data->settings->all());

            return $user;
        });
    }

    public function updateSettings(UserSettingsData $data, User $user): UserSettings
    {
        return $this->userSettingService->updateSettingsForUser($user, $data);
    }

    public function updatePassword(UserUpdatePasswordData $data, User $user): bool
    {
        return $user->update([
            'password' => $data->password,
        ]);
    }

    public function destroy(User $user): bool
    {
        $currentUser = auth()->user();
        if ($currentUser->id === $user->id) {
            throw new \Exception('You cannot delete yourself');
        }

        return $user->delete();
    }

    public function createUsersCategory(User $user, CategoryData $data): Category
    {
        return $user->categories()->create($data->all())->load('parentCategory');
    }
}
