<?php

namespace App\Services\User;

use App\Data\User\UserCreateData;
use App\Data\User\UserUpdateData;
use App\Interfaces\Services\IUserService;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final readonly class UserService implements IUserService
{
    public function index(): Collection
    {
        return User::all();
    }

    public function indexPaginated(int $perPage = 10): LengthAwarePaginator
    {
        return User::paginate($perPage);
    }

    public function store(UserCreateData $data): User
    {
        return User::firstOrCreate($data->all());
    }

    public function show(User $user): User
    {
        return $user->load('roles');
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
}
