<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class CategoryController extends BaseApiController
{
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection(Category::paginate(3));
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $category = Category::create($request->validated());
            return $this->successResponse(CategoryResource::make($category), "Category created successfully.", 201);
        } catch (\Throwable $e) {
            Log::error('CategoryController:store error: ' . $e->getMessage());
            return $this->errorResponse('Failed to create category', 500, $e);
        }
    }

    public function show(Category $category): JsonResponse
    {
        return $this->successResponse(CategoryResource::make($category), "Category retrieved successfully.");
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        try {
            $category->update($request->validated());
            return $this->successResponse(CategoryResource::make($category), "Category updated successfully.");
        } catch (\Throwable $e) {
            Log::error('CategoryController:update error: ' . $e->getMessage());
            return $this->errorResponse('Failed to update category', 500, $e);
        }
    }

    public function destroy(Category $category): JsonResponse
    {
        if ($category->posts()->exists()) {
            return $this->errorResponse('Category has posts and cannot be deleted.', 409);
        }

        try {
            $category->delete();
            return $this->successResponse(null, "Category deleted successfully.");
        } catch (\Throwable $e) {
            Log::error('CategoryController:delete error: ' . $e->getMessage());
            return $this->errorResponse('Failed to delete category', 500, $e);
        }
    }
}
