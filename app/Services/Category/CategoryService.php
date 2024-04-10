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
    public function getUsersCategories(User $user): Collection
    {
        return $user->categories()->with('parentCategory', 'icon')->get();
    }

    public function getUsersCategoriesTree(User $user): Collection
    {
        return $this->buildCategoryTree($user->categories->load('children', 'icon'));
    }

    private function buildCategoryTree(Collection $categories, ?int $parentId = null): Collection
    {
        return $categories->filter(fn (Category $category) => $category->parent_category_id === $parentId)
            ->map(fn (Category $category) => $category->setAttribute('children',
                $this->buildCategoryTree($categories, $category->id)))->values();
    }

    public function update(Category $category, CategoryData $data): Category
    {
        if ($category->type !== $data->type) {
            $category->children()->update(['type' => $data->type]);
            $category->transactions()->update(['type' => $data->type]);
            $category->children()->each(fn (Category $child) => $child->transactions()->update(['type' => $data->type]));
            $data->parent_category_id = null;
        }

        $category->update($data->all());

        return $category;
    }

    public function delete(Category $category): bool
    {
        if ($category->parent_category_id) {
            $parentCategory = $category->parentCategory;
            $transactions = $category->transactions()->get();
            $category->transactions()->detach();
            $parentCategory->transactions()->attach($transactions);
        }

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
