<?php

namespace App\Http\Controllers\Api\v1\Category;

use App\Data\Category\CategoryData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryRequest;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category\Category;
use App\Models\User;
use App\Services\Category\CategoryService;
use App\Services\User\UserService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        private readonly CategoryService $categoryService,
        private readonly UserService $userService
    )
    {
    }

    public function index(User $user)
    {
        return new CategoryCollection($this->categoryService->getUsersCategories($user));
    }

    public function store(User $user, CategoryRequest $request)
    {
        $data = CategoryData::from($request);

        return new CategoryResource($this->userService->createUsersCategory($user, $data));
    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $data = CategoryData::from($request);

        return new CategoryResource($this->categoryService->update($category, $data));
    }

    public function destroy(Category $category)
    {
        return response()->json([
            'success' => $this->categoryService->delete($category)
        ]);
    }
}
