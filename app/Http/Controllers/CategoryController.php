<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::all();

        return $this->success('Succeed get all competition categories.', [
            'categories' => $categories,
        ]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $this->authorize('create', Category::class);
        
        $category = Category::query()->create([
            'name' => $request->name,
        ]);

        return $this->success('Succeed create new competition category.', [
            'category' => $category,
        ], 201);
    }

    public function update(UpdateCategoryRequest $request, string $categoryId): JsonResponse
    {
        $competitionCategory = Category::query()->findOrFail($categoryId);
        $this->authorize('update', $competitionCategory);

        $competitionCategory->update([
            'name' => $request->name,
        ]);

        return $this->success('Succeed update competition category.', [
            'category' => $competitionCategory,
        ]);
    }

    public function destroy(string $categoryId): JsonResponse
    {
        $competitionCategory = Category::query()->findOrFail($categoryId);
        $this->authorize('delete', $competitionCategory);

        $competitionCategory->delete();

        return $this->success('Succeed delete competition category.', [
            'category' => $competitionCategory,
        ]);
    }
}
