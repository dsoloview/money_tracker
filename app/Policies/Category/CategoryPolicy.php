<?php

namespace App\Policies\Category;

use App\Models\Category\Category;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

class CategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    public function view(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    public function create(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    public function update(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    public function restore(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }

    public function forceDelete(User $user, Category $category): bool
    {
        return $user->id === $category->user_id;
    }
}
