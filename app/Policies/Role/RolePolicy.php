<?php

namespace App\Policies\Role;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Role $role): bool
    {
        return false;
    }
}
