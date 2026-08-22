<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDishRequest;
use App\Http\Requests\UpdateDishRequest;
use App\Models\Dish;
use App\Models\DishAddon;
use App\Models\DishCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DishController extends Controller
{
    public function index(Request $request): View
    {
        $dishes = Dish::query()
            ->with('category')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->value();

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('name_ru', 'like', "%{$search}%")
                        ->orWhere('name_ka', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('dish_category_id', $request->integer('category_id'));
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $categories = DishCategory::query()
            ->orderBy('sort')
            ->orderBy('name')
            ->get();

        return view('admin.dishes.index', [
            'title' => 'Блюда',
            'dishes' => $dishes,
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        $categories = DishCategory::query()
            ->where('is_active', true)
            ->orderBy('sort')
            ->orderBy('name')
            ->get();

        $addons = DishAddon::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name_ru')
            ->get();

        return view('admin.dishes.create', [
            'title' => 'Создать блюдо',
            'categories' => $categories,
            'addons' => $addons,
        ]);
    }

    public function store(StoreDishRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'addon_ids', 'addon_poster_ids', 'addon_prices']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('dishes', 'public');
        }

        $dish = Dish::create($data);
        $this->syncAddons($dish, $request->validated());

        return redirect()
            ->route('admin.dishes.index')
            ->with('success', 'Блюдо успешно создано.');
    }

    public function edit(Dish $dish): View
    {
        $categories = DishCategory::query()
            ->where('is_active', true)
            ->orderBy('sort')
            ->orderBy('name')
            ->get();

        $addons = DishAddon::query()
            ->orderBy('sort_order')
            ->orderBy('name_ru')
            ->get();

        $dish->load('addons');

        return view('admin.dishes.edit', [
            'title' => 'Редактировать блюдо',
            'dish' => $dish,
            'categories' => $categories,
            'addons' => $addons,
        ]);
    }

    public function update(UpdateDishRequest $request, Dish $dish): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'addon_ids', 'addon_poster_ids', 'addon_prices']);

        if ($request->hasFile('image')) {
            if ($dish->image) {
                Storage::disk('public')->delete($dish->image);
            }
            $data['image'] = $request->file('image')->store('dishes', 'public');
        }

        $dish->update($data);
        $this->syncAddons($dish, $request->validated());

        return redirect()
            ->route('admin.dishes.index')
            ->with('success', 'Блюдо успешно обновлено.');
    }

    public function destroy(Dish $dish): RedirectResponse
    {
        if ($dish->image) {
            Storage::disk('public')->delete($dish->image);
        }

        $dish->delete();

        return redirect()
            ->route('admin.dishes.index')
            ->with('success', 'Блюдо успешно удалено.');
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    protected function syncAddons(Dish $dish, array $validated): void
    {
        $addonIds = $validated['addon_ids'] ?? [];
        $posterIds = $validated['addon_poster_ids'] ?? [];
        $prices = $validated['addon_prices'] ?? [];

        $syncData = [];

        foreach ($addonIds as $index => $addonId) {
            $addonId = (int) $addonId;
            $syncData[$addonId] = [
                'poster_modification_id' => filled($posterIds[$addonId] ?? null) ? (int) $posterIds[$addonId] : null,
                'price' => filled($prices[$addonId] ?? null) ? $prices[$addonId] : null,
                'sort_order' => $index,
            ];
        }

        $dish->addons()->sync($syncData);
    }
}
