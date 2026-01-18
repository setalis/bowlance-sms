<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConstructorProductRequest;
use App\Http\Requests\UpdateConstructorProductRequest;
use App\Models\ConstructorCategory;
use App\Models\ConstructorProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ConstructorProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $products = ConstructorProduct::query()
            ->with('category')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.constructor-products.index', [
            'title' => 'Продукты конструктора',
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = ConstructorCategory::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.constructor-products.create', [
            'title' => 'Создать продукт конструктора',
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConstructorProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('constructor-products', 'public');
        }

        $product = ConstructorProduct::create($data);

        return redirect()
            ->route('admin.constructor-products.index')
            ->with('success', 'Продукт конструктора успешно создан.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ConstructorProduct $constructorProduct): View
    {
        $categories = ConstructorCategory::query()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.constructor-products.edit', [
            'title' => 'Редактировать продукт конструктора',
            'product' => $constructorProduct,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConstructorProductRequest $request, ConstructorProduct $constructorProduct): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($constructorProduct->image) {
                \Storage::disk('public')->delete($constructorProduct->image);
            }
            $data['image'] = $request->file('image')->store('constructor-products', 'public');
        }

        $constructorProduct->update($data);

        return redirect()
            ->route('admin.constructor-products.index')
            ->with('success', 'Продукт конструктора успешно обновлен.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ConstructorProduct $constructorProduct): RedirectResponse
    {
        if ($constructorProduct->image) {
            \Storage::disk('public')->delete($constructorProduct->image);
        }

        $constructorProduct->delete();

        return redirect()
            ->route('admin.constructor-products.index')
            ->with('success', 'Продукт конструктора успешно удален.');
    }
}
