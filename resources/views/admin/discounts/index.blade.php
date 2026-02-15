@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base-content text-2xl font-semibold">Скидки и акции</h2>
                <p class="text-base-content/70">Управление скидками (например, за самовывоз)</p>
            </div>
            <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary">
                <span class="icon-[tabler--plus] size-5"></span>
                Создать скидку
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
                            <th>Размер</th>
                            <th>Тип</th>
                            <th>Область</th>
                            <th>Статус</th>
                            <th>Создано</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($discounts as $discount)
                            <tr>
                                <td>{{ $discount->id }}</td>
                                <td>{{ $discount->name ?: '—' }}</td>
                                <td>
                                    @if($discount->type->value === 'percent')
                                        {{ number_format($discount->size, 0) }}%
                                    @else
                                        {{ number_format($discount->size, 2) }} ₾
                                    @endif
                                </td>
                                <td><span class="badge badge-soft badge-sm">{{ $discount->type->label() }}</span></td>
                                <td><code class="text-sm">{{ $discount->scope }}</code></td>
                                <td>
                                    @if($discount->is_active)
                                        <span class="badge badge-success badge-sm">Активна</span>
                                    @else
                                        <span class="badge badge-error badge-sm">Неактивна</span>
                                    @endif
                                </td>
                                <td>{{ $discount->created_at->format('d.m.Y') }}</td>
                                <td>
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.discounts.edit', $discount) }}"
                                           class="btn btn-circle btn-text btn-sm"
                                           aria-label="Редактировать">
                                            <span class="icon-[tabler--pencil] size-5"></span>
                                        </a>
                                        <form action="{{ route('admin.discounts.destroy', $discount) }}"
                                              method="POST"
                                              onsubmit="return confirm('Вы уверены, что хотите удалить эту скидку?');">
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
                                        <span class="icon-[tabler--discount] size-12 text-base-content/30"></span>
                                        <div>
                                            <p class="text-base-content/70 text-lg font-medium">Скидок пока нет</p>
                                            <p class="text-base-content/50 text-sm">Создайте скидку для самовывоза или других акций</p>
                                        </div>
                                        <a href="{{ route('admin.discounts.create') }}" class="btn btn-primary btn-sm">
                                            <span class="icon-[tabler--plus] size-4"></span>
                                            Создать скидку
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($discounts->hasPages())
            <div class="mt-4">
                {{ $discounts->links() }}
            </div>
        @endif
    </div>
@endsection
