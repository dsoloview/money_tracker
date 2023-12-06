<?php

namespace App\Interfaces\Services;

use App\Data\User\UserCreateData;
use App\Data\User\UserUpdateData;
use App\Models\User;

interface IUserService
{
    public function index();

    public function indexPaginated(int $perPage = 10);

    public function store(UserCreateData $data);

    public function show(User $user);

    public function update(UserUpdateData $data, User $user);

    public function destroy(User $user);
}
