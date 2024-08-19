<?php

namespace Tests\Feature\Controllers\Api\Controllers\Api\v1;

use App\Enums\Category\CategoryTransactionType;
use App\Models\Category\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Tests\Traits\WithTestUser;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithTestUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpTestUser();
    }

    public function testGetAllUsersCategories()
    {
        Sanctum::actingAs($this->user);
        Category::factory()->count(3)->create();
        Category::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson(route('users.categories.index', ['user' => $this->user->id]));

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'type',
                        'description',
                        'parent_category',
                        'icon',
                        'created_at',
                        'updated_at'
                    ],
                ],
            ]);
    }

    public function testShowCategory()
    {
        Sanctum::actingAs($this->user);
        $category = Category::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        $response = $this->getJson(route('categories.show', ['category' => $category->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'type',
                    'description',
                    'parent_category',
                    'created_at',
                    'updated_at'
                ],
            ]);
    }

    public function testNotShowAlienCategory()
    {
        Sanctum::actingAs($this->user);
        $category = Category::factory()->createOne();

        $response = $this->getJson(route('categories.show', ['category' => $category->id]));

        $response->assertStatus(403);
    }

    public function testUpdateCategory()
    {
        $category = Category::factory()->createOne([
            'user_id' => $this->user->id,
            'type' => CategoryTransactionType::EXPENSE->value,
        ]);
        $newCategory = Category::factory()->createOne([
            'user_id' => $this->user->id,
            'type' => CategoryTransactionType::EXPENSE->value,
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->patchJson(route('categories.update', ['category' => $category->id]), [
            'parent_category_id' => $newCategory->id,
            'icon_id' => null,
            'name' => 'Updated Category',
            'description' => 'Updated Description',
            'type' => CategoryTransactionType::EXPENSE->value
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'type',
                    'description',
                    'created_at',
                    'updated_at'
                ],
            ]);

        $this->assertDatabaseHas(Category::class, [
            'id' => $category->id,
            'parent_category_id' => $newCategory->id,
        ]);
    }

    public function testNotUpdateAlienCategory()
    {
        $category = Category::factory()->createOne();
        $newCategory = Category::factory()->createOne();

        Sanctum::actingAs($this->user);

        $response = $this->patchJson(route('categories.update', ['category' => $category->id]), [
            'parent_category_id' => $newCategory->id,
            'icon_id' => null,
            'name' => 'Updated Category',
            'description' => 'Updated Description',
            'type' => CategoryTransactionType::EXPENSE->value
        ]);

        $response->assertStatus(403);
    }

    public function testDeleteCategory()
    {
        $category = Category::factory()->createOne([
            'user_id' => $this->user->id,
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson(route('categories.destroy', ['category' => $category->id]));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing(Category::class, [
            'id' => $category->id,
        ]);
    }

    public function testNotDeleteAlienCategory()
    {
        $category = Category::factory()->createOne();

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson(route('categories.destroy', ['category' => $category->id]));

        $response->assertStatus(403);
    }
}
