<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDrinkRequest;
use App\Http\Requests\UpdateDrinkRequest;
use App\Models\Drink;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DrinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $drinks = Drink::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.drinks.index', [
            'title' => 'Напитки',
            'drinks' => $drinks,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.drinks.create', [
            'title' => 'Создать напиток',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDrinkRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('drinks', 'public');
        }

        $drink = Drink::create($data);

        return redirect()
            ->route('admin.drinks.index')
            ->with('success', 'Напиток успешно создан.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Drink $drink): View
    {
        return view('admin.drinks.edit', [
            'title' => 'Редактировать напиток',
            'drink' => $drink,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDrinkRequest $request, Drink $drink): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($drink->image) {
                \Storage::disk('public')->delete($drink->image);
            }
            $data['image'] = $request->file('image')->store('drinks', 'public');
        }

        $drink->update($data);

        return redirect()
            ->route('admin.drinks.index')
            ->with('success', 'Напиток успешно обновлен.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Drink $drink): RedirectResponse
    {
        if ($drink->image) {
            \Storage::disk('public')->delete($drink->image);
        }

        $drink->delete();

        return redirect()
            ->route('admin.drinks.index')
            ->with('success', 'Напиток успешно удален.');
    }
}
