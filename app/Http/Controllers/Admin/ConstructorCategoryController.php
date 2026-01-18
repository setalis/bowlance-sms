<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConstructorCategoryRequest;
use App\Http\Requests\UpdateConstructorCategoryRequest;
use App\Models\ConstructorCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConstructorCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = ConstructorCategory::query()
            ->withCount('products')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.constructor-categories.index', [
            'title' => 'Категории конструктора',
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.constructor-categories.create', [
            'title' => 'Создать категорию конструктора',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConstructorCategoryRequest $request): RedirectResponse
    {
        $category = ConstructorCategory::create($request->validated());

        return redirect()
            ->route('admin.constructor-categories.index')
            ->with('success', 'Категория конструктора успешно создана.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ConstructorCategory $constructorCategory): View
    {
        return view('admin.constructor-categories.edit', [
            'title' => 'Редактировать категорию конструктора',
            'category' => $constructorCategory,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConstructorCategoryRequest $request, ConstructorCategory $constructorCategory): RedirectResponse
    {
        $constructorCategory->update($request->validated());

        return redirect()
            ->route('admin.constructor-categories.index')
            ->with('success', 'Категория конструктора успешно обновлена.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConstructorCategory $constructorCategory): RedirectResponse
    {
        $constructorCategory->delete();

        return redirect()
            ->route('admin.constructor-categories.index')
            ->with('success', 'Категория конструктора успешно удалена.');
    }
}
