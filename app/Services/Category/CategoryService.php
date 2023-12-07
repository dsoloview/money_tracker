<?php

namespace App\Services\Category;

use App\Data\Category\CategoryData;
use App\Models\Category\Category;
use App\Models\User;

class CategoryService
{
    public function getUsersCategories(User $user)
    {
        return $user->categories;
    }

    public function update(Category $category, CategoryData $data): Category
    {
        $category->update($data->all());

        return $category;
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}
