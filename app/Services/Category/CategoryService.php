<?php

namespace App\Services\Category;

use App\Data\Category\CategoryData;
use App\Data\Category\DefaultCategoryData;
use App\Enums\Category\DefaultExpenseCategories;
use App\Enums\Category\DefaultIncomeCategories;
use App\Models\Category\Category;
use App\Models\Language\Language;
use App\Models\User;
use Illuminate\Support\Collection;

class CategoryService
{
    public function getUsersCategories(User $user)
    {
        return $user->categories->load('parentCategory');
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

    public function getDefaultForLanguage(Language $language): Collection
    {
        if (\Cache::has("default_categories_{$language->code}")) {
            return \Cache::get("default_categories_{$language->code}");
        }
        $languageCode = $language->code;
        $categories = collect();

        foreach (DefaultExpenseCategories::cases() as $category) {
            $categories->add(DefaultCategoryData::fromArray([
                'name' => $category->getTranslation($languageCode),
                'type' => $category->getType()->value,
            ]));
        }

        foreach (DefaultIncomeCategories::cases() as $category) {
            $categories->add(DefaultCategoryData::fromArray([
                'name' => $category->getTranslation($languageCode),
                'type' => $category->getType()->value,
            ]));
        }


        \Cache::put("default_categories_{$language->code}", $categories, 60 * 60 * 24);

        return $categories;
    }
}
