@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base-content text-2xl font-semibold">Блюда</h2>
                <p class="text-base-content/70">Управление блюдами меню</p>
            </div>
            <a href="{{ route('admin.dishes.create') }}" class="btn btn-primary">
                <span class="icon-[tabler--plus] size-5"></span>
                Создать блюдо
            </a>
        </div>

        @session('success')
            <x-ui.alert variant="success">
                {{ $value }}
            </x-ui.alert>
        @endsession

        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.dishes.index') }}" class="flex flex-wrap gap-4">
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
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <span class="icon-[tabler--search] size-5"></span>
                        Поиск
                    </button>
                    @if(request()->filled('search') || request()->filled('category_id'))
                        <a href="{{ route('admin.dishes.index') }}" class="btn btn-ghost">
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
                            <th>Сортировка</th>
                            <th>Создано</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dishes as $dish)
                            <tr>
                                <td>{{ $dish->id }}</td>
                                <td>
                                    @if($dish->image)
                                        <div class="avatar">
                                            <div class="size-20 rounded">
                                                <img src="{{ Storage::url($dish->image) }}" alt="{{ $dish->name }}" />
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
                                        <div class="font-medium">{{ $dish->name }}</div>
                                        @if($dish->description)
                                            <div class="text-base-content/60 text-sm">{{ Str::limit($dish->description, 50) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($dish->category)
                                        <span class="badge badge-outline">{{ $dish->category->name }}</span>
                                    @else
                                        <span class="text-base-content/40 text-sm">Без категории</span>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        @if($dish->discount_price)
                                            <div class="text-success font-semibold">{{ number_format($dish->discount_price, 2) }} ₾</div>
                                            <div class="text-base-content/50 text-sm line-through">{{ number_format($dish->price, 2) }} ₾</div>
                                        @else
                                            <div class="font-semibold">{{ number_format($dish->price, 2) }} ₾</div>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $dish->weight_volume ?? '—' }}</td>
                                <td>{{ $dish->calories ? $dish->calories . ' ккал' : '—' }}</td>
                                <td>{{ $dish->sort_order }}</td>
                                <td>{{ $dish->created_at->format('d.m.Y') }}</td>
                                <td>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.dishes.edit', $dish) }}" 
                                           class="btn btn-circle btn-text btn-sm" 
                                           aria-label="Редактировать">
                                            <span class="icon-[tabler--pencil] size-5"></span>
                                        </a>
                                        <form action="{{ route('admin.dishes.destroy', $dish) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Вы уверены, что хотите удалить это блюдо?');">
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
                                <td colspan="10" class="text-center py-8">
                                    <div class="flex flex-col items-center gap-4">
                                        <span class="icon-[tabler--tools-kitchen-2] size-12 text-base-content/30"></span>
                                        <div>
                                            @if(request()->filled('search') || request()->filled('category_id'))
                                                <p class="text-base-content/70 text-lg font-medium">Ничего не найдено</p>
                                                <p class="text-base-content/50 text-sm">Измените параметры поиска или сбросьте фильтры</p>
                                            @else
                                                <p class="text-base-content/70 text-lg font-medium">Блюд пока нет</p>
                                                <p class="text-base-content/50 text-sm">Создайте первое блюдо для начала работы</p>
                                            @endif
                                        </div>
                                        @if(request()->filled('search') || request()->filled('category_id'))
                                            <a href="{{ route('admin.dishes.index') }}" class="btn btn-ghost btn-sm">
                                                <span class="icon-[tabler--x] size-4"></span>
                                                Сбросить фильтры
                                            </a>
                                        @else
                                            <a href="{{ route('admin.dishes.create') }}" class="btn btn-primary btn-sm">
                                                <span class="icon-[tabler--plus] size-4"></span>
                                                Создать блюдо
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

        @if ($dishes->hasPages())
            <div class="mt-4">
                {{ $dishes->links() }}
            </div>
        @endif
    </div>
@endsection
