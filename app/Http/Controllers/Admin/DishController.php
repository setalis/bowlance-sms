<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDishRequest;
use App\Http\Requests\UpdateDishRequest;
use App\Models\Dish;
use App\Models\DishCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DishController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $dishes = Dish::query()
            ->with('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.dishes.index', [
            'title' => 'Блюда',
            'dishes' => $dishes,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = DishCategory::query()
            ->where('is_active', true)
            ->orderBy('sort')
            ->orderBy('name')
            ->get();

        return view('admin.dishes.create', [
            'title' => 'Создать блюдо',
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDishRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('dishes', 'public');
        }

        $dish = Dish::create($data);

        return redirect()
            ->route('admin.dishes.index')
            ->with('success', 'Блюдо успешно создано.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dish $dish): View
    {
        $categories = DishCategory::query()
            ->where('is_active', true)
            ->orderBy('sort')
            ->orderBy('name')
            ->get();

        return view('admin.dishes.edit', [
            'title' => 'Редактировать блюдо',
            'dish' => $dish,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDishRequest $request, Dish $dish): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($dish->image) {
                \Storage::disk('public')->delete($dish->image);
            }
            $data['image'] = $request->file('image')->store('dishes', 'public');
        }

        $dish->update($data);

        return redirect()
            ->route('admin.dishes.index')
            ->with('success', 'Блюдо успешно обновлено.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dish $dish): RedirectResponse
    {
        if ($dish->image) {
            \Storage::disk('public')->delete($dish->image);
        }

        $dish->delete();

        return redirect()
            ->route('admin.dishes.index')
            ->with('success', 'Блюдо успешно удалено.');
    }
}
