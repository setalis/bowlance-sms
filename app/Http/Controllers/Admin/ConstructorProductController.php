<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ConstructorType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConstructorProductRequest;
use App\Http\Requests\UpdateConstructorProductRequest;
use App\Models\ConstructorCategory;
use App\Models\ConstructorProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ConstructorProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = ConstructorProduct::query()
            ->with(['categories', 'variants'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->trim()->value();

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('name_ru', 'like', "%{$search}%")
                        ->orWhere('name_ka', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->whereHas(
                    'categories',
                    fn ($q) => $q->where('constructor_categories.id', $request->integer('category_id'))
                );
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $categories = ConstructorCategory::query()
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.constructor-products.index', [
            'title' => 'Продукты конструктора',
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        $categories = ConstructorCategory::query()
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.constructor-products.create', [
            'title' => 'Создать продукт конструктора',
            'categories' => $categories,
        ]);
    }

    public function store(StoreConstructorProductRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['category_ids', 'image', 'variants']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('constructor-products', 'public');
        }

        $product = ConstructorProduct::create($data);
        $product->categories()->sync($request->validated('category_ids') ?? []);
        $this->syncVariants($product, $request->selectedConstructorTypes(), $request->validated('variants', []));

        return redirect()
            ->route('admin.constructor-products.index')
            ->with('success', 'Продукт конструктора успешно создан.');
    }

    public function edit(ConstructorProduct $constructorProduct): View
    {
        $categories = ConstructorCategory::query()
            ->orderBy('type')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $constructorProduct->load(['categories', 'variants']);

        return view('admin.constructor-products.edit', [
            'title' => 'Редактировать продукт конструктора',
            'product' => $constructorProduct,
            'categories' => $categories,
        ]);
    }

    public function update(UpdateConstructorProductRequest $request, ConstructorProduct $constructorProduct): RedirectResponse
    {
        $data = $request->safe()->except(['category_ids', 'image', 'variants']);

        if ($request->hasFile('image')) {
            if ($constructorProduct->image) {
                Storage::disk('public')->delete($constructorProduct->image);
            }
            $data['image'] = $request->file('image')->store('constructor-products', 'public');
        }

        $constructorProduct->update($data);
        $constructorProduct->categories()->sync($request->validated('category_ids') ?? []);
        $this->syncVariants($constructorProduct, $request->selectedConstructorTypes(), $request->validated('variants', []));

        return redirect()
            ->route('admin.constructor-products.index')
            ->with('success', 'Продукт конструктора успешно обновлен.');
    }

    public function destroy(ConstructorProduct $constructorProduct): RedirectResponse
    {
        if ($constructorProduct->image) {
            Storage::disk('public')->delete($constructorProduct->image);
        }

        $constructorProduct->delete();

        return redirect()
            ->route('admin.constructor-products.index')
            ->with('success', 'Продукт конструктора успешно удален.');
    }

    /**
     * @param  list<ConstructorType>  $types
     * @param  array<string, array<string, mixed>>  $variants
     */
    protected function syncVariants(ConstructorProduct $product, array $types, array $variants): void
    {
        if ($types === []) {
            return;
        }

        $typeValues = collect($types)->map(fn (ConstructorType $type) => $type->value)->all();

        $product->variants()->whereNotIn('type', $typeValues)->delete();

        foreach ($types as $type) {
            $payload = $variants[$type->value] ?? [];

            $product->variants()->updateOrCreate(
                ['type' => $type],
                [
                    'price' => $payload['price'] ?? 0,
                    'weight_volume' => $payload['weight_volume'] ?? null,
                    'calories' => $payload['calories'] ?? null,
                    'proteins' => $payload['proteins'] ?? null,
                    'fats' => $payload['fats'] ?? null,
                    'carbohydrates' => $payload['carbohydrates'] ?? null,
                    'fiber' => $payload['fiber'] ?? null,
                    'poster_modification_id' => $payload['poster_modification_id'] ?? null,
                ]
            );
        }
    }
}
