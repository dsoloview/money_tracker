<?php

namespace Tests\Unit\Services\Category;

use App\Data\Category\CategoryData;
use App\Enums\Category\CategoryTransactionType;
use App\Enums\Category\DefaultExpenseCategories;
use App\Enums\Category\DefaultIncomeCategories;
use App\Models\Category\Category;
use App\Models\Language\Language;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Services\Category\CategoryService;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\SeededTestCase;

class CategoryServiceTest extends SeededTestCase
{
    private CategoryService $categoryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryService = app(CategoryService::class);
    }

    public function testGetUsersCategories()
    {
        $user = User::factory()->hasCategories(3)->create();

        $categories = $this->categoryService->getUsersCategories($user);

        $this->assertInstanceOf(Collection::class, $categories);
        $this->assertCount(3, $categories);
        $this->assertTrue($categories->first()->relationLoaded('parentCategory'));
        $this->assertTrue($categories->first()->relationLoaded('icon'));
    }

    public function testGetUsersCategoriesTree()
    {
        $user = User::factory()->hasCategories(3)->create();

        $parentCategory = $user->categories()->first();
        $childCategory1 = Category::factory()->create([
            'parent_category_id' => $parentCategory->id, 'user_id' => $user->id
        ]);
        $childCategory2 = Category::factory()->create([
            'parent_category_id' => $childCategory1->id, 'user_id' => $user->id
        ]);

        $tree = $this->categoryService->getUsersCategoriesTree($user);

        $this->assertInstanceOf(Collection::class, $tree);
        $this->assertEquals($parentCategory->id, $tree->first()->id);
        $this->assertEquals($childCategory1->id, $tree->first()->children->first()->id);
        $this->assertEquals($childCategory2->id, $tree->first()->children->first()->children->first()->id);
    }

    public function testGetUsersCategoriesByType()
    {
        $user = User::factory()->create();
        $expenseType = CategoryTransactionType::EXPENSE;
        $incomeType = CategoryTransactionType::INCOME;

        $expenseCategory = Category::factory()->create(['type' => $expenseType, 'user_id' => $user->id]);
        $incomeCategory = Category::factory()->create(['type' => $incomeType, 'user_id' => $user->id]);

        $expenseCategories = $this->categoryService->getUsersCategoriesByType($user, $expenseType);
        $incomeCategories = $this->categoryService->getUsersCategoriesByType($user, $incomeType);


        $this->assertInstanceOf(Collection::class, $expenseCategories);
        $this->assertCount(1, $expenseCategories);
        $this->assertEquals($expenseType, $expenseCategories->first()->type);
        $this->assertEquals($expenseCategory->id, $expenseCategories->first()->id);

        $this->assertInstanceOf(Collection::class, $incomeCategories);
        $this->assertCount(1, $incomeCategories);
        $this->assertEquals($incomeType, $incomeCategories->first()->type);
        $this->assertEquals($incomeCategory->id, $incomeCategories->first()->id);
    }

    public function testGroupCategoriesByType()
    {
        $user = User::factory()->create();
        $expenseType = CategoryTransactionType::EXPENSE;
        $incomeType = CategoryTransactionType::INCOME;

        $categories = Category::factory()->count(3)->state(new Sequence(
            ['type' => $expenseType, 'user_id' => $user->id],
            ['type' => $incomeType, 'user_id' => $user->id],
            ['type' => $expenseType, 'user_id' => $user->id],
        ))->create();

        $grouped = $this->categoryService->groupCategoriesByType($categories);

        $this->assertInstanceOf(Collection::class, $grouped);
        $this->assertCount(2, $grouped);
        $this->assertCount(2, $grouped[$expenseType->value]);
        $this->assertCount(1, $grouped[$incomeType->value]);
    }

    public function testUpdateCategoryWithNoParent()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $data = new CategoryData(
            parent_category_id: null,
            icon_id: null,
            type: $category->type->value,
            name: 'Updated Category',
            description: 'Updated Description',
        );

        $updatedCategory = $this->categoryService->update($category, $data);

        $this->assertEquals('Updated Category', $updatedCategory->name);
        $this->assertNull($updatedCategory->parent_category_id);
    }

    public function testUpdateCategoryWithParentAndNoChildren()
    {
        $user = User::factory()->create();
        $parentCategory = Category::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'parent_category_id' => $parentCategory->id,
        ]);

        $data = new CategoryData(
            parent_category_id: $parentCategory->id,
            icon_id: null,
            type: $category->type->value,
            name: 'Updated Category',
            description: 'Updated Description',
        );

        $updatedCategory = $this->categoryService->update($category, $data);

        $this->assertEquals('Updated Category', $updatedCategory->name);
        $this->assertEquals($parentCategory->id, $updatedCategory->parent_category_id);
    }

    public function testUpdateCategoryWithChildren()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $childCategory = Category::factory()->create([
            'user_id' => $user->id,
            'parent_category_id' => $category->id,
        ]);

        $data = new CategoryData(
            parent_category_id: null,
            icon_id: null,
            type: $category->type->value,
            name: 'Updated Category',
            description: 'Updated Description',
        );

        $updatedCategory = $this->categoryService->update($category, $data);

        $this->assertEquals('Updated Category', $updatedCategory->name);
        $this->assertNull($updatedCategory->parent_category_id);
        $this->assertEquals($category->id, $childCategory->fresh()->parent_category_id);
    }

    public function testUpdateCategoryTypeAndPropagateChangesToChildren()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'type' => CategoryTransactionType::EXPENSE
        ]);
        $childCategory = Category::factory()->create([
            'user_id' => $user->id,
            'parent_category_id' => $category->id,
            'type' => CategoryTransactionType::EXPENSE
        ]);

        $data = new CategoryData(
            parent_category_id: null,
            icon_id: null,
            type: CategoryTransactionType::INCOME->value,
            name: 'Updated Category',
            description: 'Updated Description',
        );

        $updatedCategory = $this->categoryService->update($category, $data);

        $this->assertEquals(CategoryTransactionType::INCOME, $updatedCategory->type);
        $this->assertEquals(CategoryTransactionType::INCOME, $childCategory->fresh()->type);
    }

    public function testUpdateCategoryTransactionsUpdatedForCategoryAndChildren()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'type' => CategoryTransactionType::EXPENSE
        ]);
        $childCategory = Category::factory()->create([
            'user_id' => $user->id,
            'parent_category_id' => $category->id,
            'type' => CategoryTransactionType::EXPENSE
        ]);

        $transaction = Transaction::factory()->create([
            'type' => CategoryTransactionType::EXPENSE
        ]);
        $childTransaction = Transaction::factory()->create([
            'type' => CategoryTransactionType::EXPENSE
        ]);

        $category->transactions()->attach($transaction);
        $childCategory->transactions()->attach($childTransaction);

        $data = new CategoryData(
            parent_category_id: null,
            icon_id: null,
            type: CategoryTransactionType::INCOME->value,
            name: 'Updated Category',
            description: 'Updated Description',
        );

        $this->categoryService->update($category, $data);

        $this->assertEquals(CategoryTransactionType::INCOME, $transaction->fresh()->type);
        $this->assertEquals(CategoryTransactionType::INCOME, $childTransaction->fresh()->type);
    }

    public function testDeleteCategoryWithNoParentAndNoChildren()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $deleted = $this->categoryService->delete($category);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function testDeleteCategoryWithParentAndNoChildren()
    {
        $user = User::factory()->create();
        $parentCategory = Category::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create(['parent_category_id' => $parentCategory->id, 'user_id' => $user->id]);

        $deleted = $this->categoryService->delete($category);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('categories', ['id' => $parentCategory->id]);
    }

    public function testDeleteCategoryWithChildren()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $childCategory = Category::factory()->create(['parent_category_id' => $category->id, 'user_id' => $user->id]);

        $deleted = $this->categoryService->delete($category);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('categories', ['id' => $childCategory->id]);
        $this->assertNull($childCategory->fresh()->parent_category_id);
    }

    public function testDeleteCategoryWithTransactionsMovedToParent()
    {
        $user = User::factory()->create();
        $parentCategory = Category::factory()->create(['user_id' => $user->id]);
        $category = Category::factory()->create(['parent_category_id' => $parentCategory->id, 'user_id' => $user->id]);

        $transaction = Transaction::factory()->create();
        $category->transactions()->attach($transaction);

        $deleted = $this->categoryService->delete($category);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
        $this->assertDatabaseHas('categories_transactions', [
            'category_id' => $parentCategory->id,
            'transaction_id' => $transaction->id
        ]);

    }

    public function testDeleteCategoryWithTransactionsAndNoParent()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $transaction = Transaction::factory()->create();
        $category->transactions()->attach($transaction);

        $deleted = $this->categoryService->delete($category);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
        $this->assertDatabaseMissing('categories_transactions', [
            'transaction_id' => $transaction->id
        ]);
    }

    public function testGetDefaultForLanguage()
    {
        $language = Language::where('code', 'en')->first();

        Cache::shouldReceive('has')->once()->with("default_categories_en")->andReturn(false);
        Cache::shouldReceive('put')->once()->with("default_categories_en", Mockery::type(Collection::class),
            60 * 60 * 24);

        $categories = $this->categoryService->getDefaultForLanguage($language);

        $this->assertInstanceOf(Collection::class, $categories);
        $this->assertNotEmpty($categories);
        $this->assertEquals(DefaultExpenseCategories::count() + DefaultIncomeCategories::count(), $categories->count());
    }
}
