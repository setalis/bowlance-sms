@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">{{ $title }}</h1>
    </div>

    <!-- Фильтры -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Поиск по номеру, имени, телефону..." 
                           class="input input-bordered w-full">
                </div>
                <div class="min-w-[180px]">
                    <select name="status" class="select select-bordered w-full">
                        <option value="">Все статусы</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <span class="icon-[tabler--search] size-5"></span>
                    Поиск
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost">
                        <span class="icon-[tabler--x] size-5"></span>
                        Сбросить
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Список заказов -->
    <div class="card">
        <div class="card-body p-0">
            @if($orders->isEmpty())
                <div class="p-6 text-center text-base-content/60">
                    <span class="icon-[tabler--shopping-cart-off] size-16 mb-4 inline-block"></span>
                    <p>Заказов пока нет</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Номер заказа</th>
                                <th>Клиент</th>
                                <th>Телефон</th>
                                <th>Сумма</th>
                                <th>Статус</th>
                                <th>Дата</th>
                                <th class="text-right">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $order) }}" class="link link-primary font-medium">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->customer_phone }}</td>
                                    <td class="font-bold">{{ number_format($order->total, 2) }} ₾</td>
                                    <td>
                                        <span class="badge badge-{{ $order->status->color() }}">
                                            {{ $order->status->label() }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                    <td class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.orders.show', $order) }}" 
                                               class="btn btn-sm btn-ghost"
                                               title="Просмотр">
                                                <span class="icon-[tabler--eye] size-4"></span>
                                            </a>
                                            <a href="{{ route('admin.orders.edit', $order) }}" 
                                               class="btn btn-sm btn-ghost"
                                               title="Редактировать">
                                                <span class="icon-[tabler--edit] size-4"></span>
                                            </a>
                                            <form action="{{ route('admin.orders.destroy', $order) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Вы уверены, что хотите удалить этот заказ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-ghost text-error" title="Удалить">
                                                    <span class="icon-[tabler--trash] size-4"></span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                @if($orders->hasPages())
                    <div class="p-4 border-t">
                        {{ $orders->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
