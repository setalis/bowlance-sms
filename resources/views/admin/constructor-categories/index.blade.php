@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base-content text-2xl font-semibold">Категории конструктора</h2>
                <p class="text-base-content/70">Управление категориями конструктора боулов</p>
            </div>
            <a href="{{ route('admin.constructor-categories.create') }}" class="btn btn-primary">
                <span class="icon-[tabler--plus] size-5"></span>
                Создать категорию
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
                            <th>Название</th>
                            <th>Продуктов</th>
                            <th>Сортировка</th>
                            <th>Создано</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>
                                    <div class="font-medium">{{ $category->name }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-outline">{{ $category->products_count }}</span>
                                </td>
                                <td>{{ $category->sort_order }}</td>
                                <td>{{ $category->created_at->format('d.m.Y') }}</td>
                                <td>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.constructor-categories.edit', $category) }}" 
                                           class="btn btn-circle btn-text btn-sm" 
                                           aria-label="Редактировать">
                                            <span class="icon-[tabler--pencil] size-5"></span>
                                        </a>
                                        <form action="{{ route('admin.constructor-categories.destroy', $category) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Вы уверены, что хотите удалить эту категорию?');">
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
                                <td colspan="6" class="text-center py-8">
                                    <div class="flex flex-col items-center gap-4">
                                        <span class="icon-[tabler--category] size-12 text-base-content/30"></span>
                                        <div>
                                            <p class="text-base-content/70 text-lg font-medium">Категорий пока нет</p>
                                            <p class="text-base-content/50 text-sm">Создайте первую категорию для начала работы</p>
                                        </div>
                                        <a href="{{ route('admin.constructor-categories.create') }}" class="btn btn-primary btn-sm">
                                            <span class="icon-[tabler--plus] size-4"></span>
                                            Создать категорию
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($categories->hasPages())
            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
@endsection
