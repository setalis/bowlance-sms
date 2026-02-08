@extends('layouts.app')

@section('content')
    <!-- Статистика -->
    <div class="shadow-base-300/10 rounded-box bg-base-100 flex flex-wrap gap-6 p-6 shadow-md">
        <!-- Пользователи -->
        <div class="flex flex-1 min-w-[180px] flex-col gap-2">
            <div class="text-base-content flex items-center gap-2">
                <div class="avatar avatar-placeholder">
                    <div class="bg-base-200 rounded-field size-9">
                        <span class="icon-[tabler--users] size-5"></span>
                    </div>
                </div>
                <h5 class="text-lg font-medium">Пользователи</h5>
            </div>
            <div>
                <div class="text-base-content text-xl font-semibold">{{ number_format($stats['users_count']) }}</div>
                <a href="{{ route('admin.users.index') }}" class="text-primary text-sm link link-hover">Управление →</a>
            </div>
        </div>

        <div class="divider sm:divider-horizontal"></div>

        <!-- Заказы -->
        <div class="flex flex-1 min-w-[180px] flex-col gap-2">
            <div class="text-base-content flex items-center gap-2">
                <div class="avatar avatar-placeholder">
                    <div class="bg-base-200 rounded-field size-9">
                        <span class="icon-[tabler--shopping-cart] size-5"></span>
                    </div>
                </div>
                <h5 class="text-lg font-medium">Заказы</h5>
            </div>
            <div>
                <div class="text-base-content text-xl font-semibold">{{ number_format($stats['orders_count']) }}</div>
                <div class="text-base-content/70 text-sm">
                    Новых: {{ $stats['orders_new_count'] }} · В работе: {{ $stats['orders_in_progress_count'] }} · Выполнено: {{ $stats['orders_completed_count'] }}
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-primary text-sm link link-hover">Управление →</a>
            </div>
        </div>

        <div class="divider sm:divider-horizontal"></div>

        <!-- Выручка -->
        <div class="flex flex-1 min-w-[180px] flex-col gap-2">
            <div class="text-base-content flex items-center gap-2">
                <div class="avatar avatar-placeholder">
                    <div class="bg-base-200 rounded-field size-9">
                        <span class="icon-[tabler--currency-dollar] size-5"></span>
                    </div>
                </div>
                <h5 class="text-lg font-medium">Выручка</h5>
            </div>
            <div>
                <div class="text-base-content text-xl font-semibold">{{ number_format($stats['revenue'], 2) }} ₾</div>
                <span class="text-base-content/70 text-sm">За выполненные заказы</span>
            </div>
        </div>
    </div>

    <!-- Каталог: Блюда, Напитки, Конструктор -->
    <div class="shadow-base-300/10 rounded-box bg-base-100 flex flex-wrap gap-6 p-6 shadow-md">
        <div class="flex flex-1 min-w-[160px] flex-col gap-2">
            <div class="text-base-content flex items-center gap-2">
                <span class="icon-[tabler--salad] size-5"></span>
                <h5 class="text-lg font-medium">Блюда</h5>
            </div>
            <div>
                <div class="text-base-content text-xl font-semibold">{{ $stats['dishes_count'] }}</div>
                <span class="text-base-content/70 text-sm">в {{ $stats['dish_categories_count'] }} категориях</span>
                <a href="{{ route('admin.dishes.index') }}" class="block text-primary text-sm link link-hover">Управление →</a>
            </div>
        </div>

        <div class="divider sm:divider-horizontal"></div>

        <div class="flex flex-1 min-w-[160px] flex-col gap-2">
            <div class="text-base-content flex items-center gap-2">
                <span class="icon-[tabler--cup] size-5"></span>
                <h5 class="text-lg font-medium">Напитки</h5>
            </div>
            <div>
                <div class="text-base-content text-xl font-semibold">{{ $stats['drinks_count'] }}</div>
                <a href="{{ route('admin.drinks.index') }}" class="text-primary text-sm link link-hover">Управление →</a>
            </div>
        </div>

        <div class="divider sm:divider-horizontal"></div>

        <div class="flex flex-1 min-w-[160px] flex-col gap-2">
            <div class="text-base-content flex items-center gap-2">
                <span class="icon-[tabler--settings] size-5"></span>
                <h5 class="text-lg font-medium">Конструктор</h5>
            </div>
            <div>
                <div class="text-base-content text-xl font-semibold">{{ $stats['constructor_products_count'] }}</div>
                <span class="text-base-content/70 text-sm">в {{ $stats['constructor_categories_count'] }} категориях</span>
                <a href="{{ route('admin.constructor-products.index') }}" class="block text-primary text-sm link link-hover">Управление →</a>
            </div>
        </div>
    </div>

    <!-- Последние заказы -->
    <div class="shadow-base-300/10 rounded-box bg-base-100 p-6 shadow-md">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold">Последние заказы</h2>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost btn-sm">Все заказы →</a>
        </div>
        @if($recentOrders->isEmpty())
            <div class="py-8 text-center text-base-content/60">
                <span class="icon-[tabler--shopping-cart-off] size-16 mb-4 inline-block"></span>
                <p>Заказов пока нет</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Номер</th>
                            <th>Клиент</th>
                            <th>Телефон</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                            <th>Дата</th>
                            <th class="text-right">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentOrders as $order)
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
                                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-ghost btn-sm">
                                        <span class="icon-[tabler--eye] size-4"></span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
