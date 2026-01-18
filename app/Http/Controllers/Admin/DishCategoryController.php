<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDishCategoryRequest;
use App\Http\Requests\UpdateDishCategoryRequest;
use App\Models\DishCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DishCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = DishCategory::query()
            ->orderBy('sort')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.categories.index', [
            'title' => 'Категории блюд',
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.categories.create', [
            'title' => 'Создать категорию',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDishCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category = DishCategory::create($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Категория успешно создана.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DishCategory $category): View
    {
        return view('admin.categories.edit', [
            'title' => 'Редактировать категорию',
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDishCategoryRequest $request, DishCategory $category): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($category->image) {
                \Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Категория успешно обновлена.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DishCategory $category): RedirectResponse
    {
        if ($category->image) {
            \Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Категория успешно удалена.');
    }
}
