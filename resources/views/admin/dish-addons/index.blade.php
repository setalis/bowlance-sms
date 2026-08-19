@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base-content text-2xl font-semibold">Добавки к блюдам</h2>
                <p class="text-base-content/70">Справочник опциональных добавок (модификаторы Poster)</p>
            </div>
            <a href="{{ route('admin.dish-addons.create') }}" class="btn btn-primary">
                <span class="icon-[tabler--plus] size-5"></span>
                Создать добавку
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
                            <th>Цена</th>
                            <th>Ккал</th>
                            <th>Сортировка</th>
                            <th>Статус</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($addons as $addon)
                            <tr>
                                <td>{{ $addon->id }}</td>
                                <td class="font-medium">{{ $addon->name }}</td>
                                <td>{{ number_format($addon->price, 2) }} ₾</td>
                                <td>{{ $addon->calories !== null ? $addon->calories.' ккал' : '—' }}</td>
                                <td>{{ $addon->sort_order }}</td>
                                <td>
                                    @if($addon->is_active)
                                        <span class="badge badge-success badge-soft">Активна</span>
                                    @else
                                        <span class="badge badge-ghost">Неактивна</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.dish-addons.edit', $addon) }}"
                                           class="btn btn-circle btn-text btn-sm"
                                           aria-label="Редактировать">
                                            <span class="icon-[tabler--pencil] size-5"></span>
                                        </a>
                                        <form action="{{ route('admin.dish-addons.destroy', $addon) }}"
                                              method="POST"
                                              onsubmit="return confirm('Удалить эту добавку?');">
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
                                <td colspan="7" class="text-center py-8 text-base-content/60">Добавок пока нет</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($addons->hasPages())
            <div class="mt-4">
                {{ $addons->links() }}
            </div>
        @endif
    </div>
@endsection
