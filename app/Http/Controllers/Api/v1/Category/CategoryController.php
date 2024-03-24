<?php

namespace App\Http\Controllers\Api\v1\Category;

use App\Data\Category\CategoryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryRequest;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Category\DefaultCategory\DefaultCategoryCollection;
use App\Models\Category\Category;
use App\Models\User;
use App\Services\Category\CategoryService;
use App\Services\User\UserService;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'Category', description: 'Category management')]
#[Authenticated]
class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly UserService $userService
    ) {
    }

    #[Endpoint('Get all categories for a user')]
    #[ResponseFromApiResource(CategoryCollection::class, Category::class)]
    public function index(User $user): CategoryCollection
    {
        $this->authorize('viewAny', [Category::class, $user]);
        return new CategoryCollection($this->categoryService->getUsersCategoriesTree($user));
    }

    #[Endpoint('Create a new category for a user')]
    #[ResponseFromApiResource(CategoryResource::class, Category::class)]
    public function store(User $user, CategoryRequest $request)
    {
        $this->authorize('create', [Category::class, $user]);
        $data = CategoryData::from($request);

        return new CategoryResource($this->userService->createUsersCategory($user, $data));
    }

    #[Endpoint('Get a category by id')]
    #[ResponseFromApiResource(CategoryResource::class, Category::class)]
    public function show(Category $category)
    {
        $this->authorize('view', $category);
        return new CategoryResource($category->load('parentCategory'));
    }

    #[Endpoint('Update a category')]
    #[ResponseFromApiResource(CategoryResource::class, Category::class)]
    public function update(CategoryRequest $request, Category $category)
    {
        $this->authorize('update', $category);
        $data = CategoryData::from($request);

        return new CategoryResource($this->categoryService->update($category, $data));
    }

    #[Endpoint('Delete a category')]
    #[Response(['success' => true])]
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        return response()->json([
            'success' => $this->categoryService->delete($category),
        ]);
    }

    #[Endpoint('Get default categories for a user')]
    #[ResponseFromApiResource(DefaultCategoryCollection::class, Category::class)]
    public function default(User $user): DefaultCategoryCollection
    {
        $categories = $this->categoryService->getDefaultForLanguage($user->language);

        return new DefaultCategoryCollection($categories);
    }
}
