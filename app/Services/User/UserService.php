<?php

namespace App\Services\User;

use App\Data\User\UserCreateData;
use App\Data\User\UserUpdateData;
use App\Interfaces\Services\IUserService;
use App\Models\User;
use Illuminate\Support\Collection;

final readonly class UserService implements IUserService
{
    public function index(): Collection
    {
        return User::all();
    }

    public function store(UserCreateData $data): User
    {
        return User::firstOrCreate($data->all());
    }

    public function show(User $user): User
    {
        return $user;
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
