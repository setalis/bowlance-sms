@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base-content text-2xl font-semibold">Продукты конструктора</h2>
                <p class="text-base-content/70">Управление продуктами для конструкторов боулов и завтраков</p>
            </div>
            <a href="{{ route('admin.constructor-products.create') }}" class="btn btn-primary">
                <span class="icon-[tabler--plus] size-5"></span>
                Создать продукт
            </a>
        </div>

        @session('success')
            <x-ui.alert variant="success">
                {{ $value }}
            </x-ui.alert>
        @endsession

        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.constructor-products.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Поиск по названию..."
                               class="input input-bordered w-full">
                    </div>
                    <div class="min-w-[220px]">
                        <select name="category_id" class="select select-bordered w-full">
                            <option value="">Все категории</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('category_id') === (string) $category->id)>
                                    {{ $category->name }} ({{ $category->type === \App\Enums\ConstructorType::Bowl ? 'боулы' : 'завтраки' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <span class="icon-[tabler--search] size-5"></span>
                        Поиск
                    </button>
                    @if(request()->filled('search') || request()->filled('category_id'))
                        <a href="{{ route('admin.constructor-products.index') }}" class="btn btn-ghost">
                            <span class="icon-[tabler--x] size-5"></span>
                            Сбросить
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <div class="rounded-box shadow-base-300/10 bg-base-100 w-full pb-2 shadow-md">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Изображение</th>
                            <th>Название</th>
                            <th>Категория</th>
                            <th>Цена</th>
                            <th>Вес/Объем</th>
                            <th>Калории</th>
                            <!-- <th>Сортировка</th> -->
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>
                                    @if($product->image)
                                        <div class="avatar">
                                            <div class="size-20 rounded">
                                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" />
                                            </div>
                                        </div>
                                    @else
                                        <div class="avatar avatar-placeholder">
                                            <div class="bg-base-200 size-12 rounded">
                                                <span class="icon-[tabler--photo] size-6"></span>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <div class="font-medium">{{ $product->name }}</div>
                                        @if($product->description)
                                            <div class="text-base-content/60 text-sm">{{ Str::limit($product->description, 50) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($product->categories->isNotEmpty())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($product->categories as $category)
                                                <span class="badge badge-outline" title="{{ $category->type->label() }}">
                                                    {{ $category->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-base-content/40 text-sm">Без категории</span>
                                    @endif
                                </td>
                                <td>
                                    @forelse ($product->variants->sortBy(fn ($variant) => $variant->type->value) as $variant)
                                        <div class="text-sm {{ ! $loop->first ? 'mt-1' : '' }}">
                                            <span class="text-base-content/60">{{ $variant->type === \App\Enums\ConstructorType::Bowl ? 'Боул' : 'Завтрак' }}:</span>
                                            <span class="font-semibold">{{ number_format($variant->price, 2) }} ₾</span>
                                        </div>
                                    @empty
                                        <span class="text-base-content/40">—</span>
                                    @endforelse
                                </td>
                                <td>
                                    @forelse ($product->variants->sortBy(fn ($variant) => $variant->type->value) as $variant)
                                        <div class="text-sm {{ ! $loop->first ? 'mt-1' : '' }}">{{ $variant->weight_volume ?? '—' }}</div>
                                    @empty
                                        —
                                    @endforelse
                                </td>
                                <td>
                                    @forelse ($product->variants->sortBy(fn ($variant) => $variant->type->value) as $variant)
                                        <div class="text-sm {{ ! $loop->first ? 'mt-1' : '' }}">{{ $variant->calories ? $variant->calories . ' ккал' : '—' }}</div>
                                    @empty
                                        —
                                    @endforelse
                                </td>
                                <!-- <td>{{ $product->sort_order }}</td> -->
                                <td>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.constructor-products.edit', $product) }}" 
                                           class="btn btn-circle btn-text btn-sm" 
                                           aria-label="Редактировать">
                                            <span class="icon-[tabler--pencil] size-5"></span>
                                        </a>
                                        <form action="{{ route('admin.constructor-products.destroy', $product) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Вы уверены, что хотите удалить этот продукт?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-circle btn-text btn-sm text-error" 
                                                    aria-label="Удалить">
                                                <span class="icon-[tabler--trash] size-5"></span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8">
                                    <div class="flex flex-col items-center gap-4">
                                        <span class="icon-[tabler--bowl] size-12 text-base-content/30"></span>
                                        <div>
                                            @if(request()->filled('search') || request()->filled('category_id'))
                                                <p class="text-base-content/70 text-lg font-medium">Ничего не найдено</p>
                                                <p class="text-base-content/50 text-sm">Измените параметры поиска или сбросьте фильтры</p>
                                            @else
                                                <p class="text-base-content/70 text-lg font-medium">Продуктов пока нет</p>
                                                <p class="text-base-content/50 text-sm">Создайте первый продукт для начала работы</p>
                                            @endif
                                        </div>
                                        @if(request()->filled('search') || request()->filled('category_id'))
                                            <a href="{{ route('admin.constructor-products.index') }}" class="btn btn-ghost btn-sm">
                                                <span class="icon-[tabler--x] size-4"></span>
                                                Сбросить фильтры
                                            </a>
                                        @else
                                            <a href="{{ route('admin.constructor-products.create') }}" class="btn btn-primary btn-sm">
                                                <span class="icon-[tabler--plus] size-4"></span>
                                                Создать продукт
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($products->hasPages())
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
@endsection
