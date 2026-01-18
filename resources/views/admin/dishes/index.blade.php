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
                                            <div class="text-success font-semibold">{{ number_format($dish->discount_price, 2) }} ₽</div>
                                            <div class="text-base-content/50 text-sm line-through">{{ number_format($dish->price, 2) }} ₽</div>
                                        @else
                                            <div class="font-semibold">{{ number_format($dish->price, 2) }} ₽</div>
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
                                            <p class="text-base-content/70 text-lg font-medium">Блюд пока нет</p>
                                            <p class="text-base-content/50 text-sm">Создайте первое блюдо для начала работы</p>
                                        </div>
                                        <a href="{{ route('admin.dishes.create') }}" class="btn btn-primary btn-sm">
                                            <span class="icon-[tabler--plus] size-4"></span>
                                            Создать блюдо
                                        </a>
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
