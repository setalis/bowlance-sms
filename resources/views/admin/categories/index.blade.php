@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base-content text-2xl font-semibold">Категории блюд</h2>
                <p class="text-base-content/70">Управление категориями меню</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
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
                            <th>Изображение</th>
                            <th>Название</th>
                            <th>Slug</th>
                            <th>Сортировка</th>
                            <th>Статус</th>
                            <th>Создано</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>
                                    @if($category->image)
                                        <div class="avatar">
                                            <div class="size-20 rounded">
                                                <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" />
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
                                        <div class="font-medium">{{ $category->name }}</div>
                                        @if($category->description)
                                            <div class="text-base-content/60 text-sm">{{ Str::limit($category->description, 50) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td><code class="text-sm">{{ $category->slug }}</code></td>
                                <td>{{ $category->sort }}</td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge badge-success badge-sm">Активна</span>
                                    @else
                                        <span class="badge badge-error badge-sm">Неактивна</span>
                                    @endif
                                </td>
                                <td>{{ $category->created_at->format('d.m.Y') }}</td>
                                <td>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}" 
                                           class="btn btn-circle btn-text btn-sm" 
                                           aria-label="Редактировать">
                                            <span class="icon-[tabler--pencil] size-5"></span>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}" 
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
                                <td colspan="8" class="text-center py-8">
                                    <div class="flex flex-col items-center gap-4">
                                        <span class="icon-[tabler--category] size-12 text-base-content/30"></span>
                                        <div>
                                            <p class="text-base-content/70 text-lg font-medium">Категорий пока нет</p>
                                            <p class="text-base-content/50 text-sm">Создайте первую категорию для начала работы</p>
                                        </div>
                                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm">
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
