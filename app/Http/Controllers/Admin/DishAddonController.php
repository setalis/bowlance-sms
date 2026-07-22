<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDishAddonRequest;
use App\Http\Requests\UpdateDishAddonRequest;
use App\Models\DishAddon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DishAddonController extends Controller
{
    public function index(): View
    {
        $addons = DishAddon::query()
            ->orderBy('sort_order')
            ->orderBy('name_ru')
            ->paginate(15);

        return view('admin.dish-addons.index', [
            'title' => 'Добавки к блюдам',
            'addons' => $addons,
        ]);
    }

    public function create(): View
    {
        return view('admin.dish-addons.create', [
            'title' => 'Создать добавку',
        ]);
    }

    public function store(StoreDishAddonRequest $request): RedirectResponse
    {
        DishAddon::create($request->validated());

        return redirect()
            ->route('admin.dish-addons.index')
            ->with('success', 'Добавка успешно создана.');
    }

    public function edit(DishAddon $dishAddon): View
    {
        return view('admin.dish-addons.edit', [
            'title' => 'Редактировать добавку',
            'addon' => $dishAddon,
        ]);
    }

    public function update(UpdateDishAddonRequest $request, DishAddon $dishAddon): RedirectResponse
    {
        $dishAddon->update($request->validated());

        return redirect()
            ->route('admin.dish-addons.index')
            ->with('success', 'Добавка успешно обновлена.');
    }

    public function destroy(DishAddon $dishAddon): RedirectResponse
    {
        $dishAddon->delete();

        return redirect()
            ->route('admin.dish-addons.index')
            ->with('success', 'Добавка успешно удалена.');
    }
}
