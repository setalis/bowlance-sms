@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost btn-sm mb-2">
                <span class="icon-[tabler--arrow-left] size-4"></span>
                Назад к списку
            </a>
            <h1 class="text-2xl font-bold">{{ $title }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.orders.edit', $order) }}" class="btn btn-primary btn-sm gap-2">
                <span class="icon-[tabler--edit] size-4"></span>
                Редактировать
            </a>
            <form action="{{ route('admin.orders.destroy', $order) }}" 
                  method="POST" 
                  onsubmit="return confirm('Вы уверены, что хотите удалить этот заказ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-error btn-sm gap-2">
                    <span class="icon-[tabler--trash] size-4"></span>
                    Удалить
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Основная информация -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Позиции заказа -->
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Состав заказа</h2>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex gap-4 p-4 rounded-lg bg-base-200/50">
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <h3 class="font-medium">{{ $item->name }}</h3>
                                            @if($item->item_type === 'bowl' && $item->bowl_products)
                                                <div class="mt-2">
                                                    <p class="text-xs text-base-content/60 mb-1">Состав боула:</p>
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($item->bowl_products as $product)
                                                            <span class="badge badge-sm badge-outline">{{ $product['name'] }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="badge badge-primary">{{ $item->item_type === 'dish' ? 'Блюдо' : 'Боул' }}</span>
                                    </div>
                                    
                                    @if($item->calories || $item->proteins || $item->fats || $item->carbohydrates)
                                        <div class="flex flex-wrap gap-2 text-xs mt-2">
                                            @if($item->calories)
                                                <span class="badge badge-outline badge-xs">{{ $item->calories }} ккал</span>
                                            @endif
                                            @if($item->proteins)
                                                <span class="badge badge-outline badge-xs">Б: {{ $item->proteins }}г</span>
                                            @endif
                                            @if($item->fats)
                                                <span class="badge badge-outline badge-xs">Ж: {{ $item->fats }}г</span>
                                            @endif
                                            @if($item->carbohydrates)
                                                <span class="badge badge-outline badge-xs">У: {{ $item->carbohydrates }}г</span>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <div class="mt-3 flex items-center justify-between">
                                        <span class="text-sm text-base-content/60">{{ number_format($item->price, 2) }} ₾ × {{ $item->quantity }}</span>
                                        <span class="font-bold">{{ number_format($item->subtotal, 2) }} ₾</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Итоги -->
                    <div class="mt-6 pt-4 border-t space-y-2">
                        <div class="flex justify-between">
                            <span>Подытог:</span>
                            <span class="font-medium">{{ number_format($order->subtotal, 2) }} ₾</span>
                        </div>
                        @if($order->delivery_fee > 0)
                            <div class="flex justify-between">
                                <span>Доставка:</span>
                                <span class="font-medium">{{ number_format($order->delivery_fee, 2) }} ₾</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold pt-2 border-t">
                            <span>Итого:</span>
                            <span class="text-primary">{{ number_format($order->total, 2) }} ₾</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Боковая панель -->
        <div class="space-y-6">
            <!-- Статус заказа -->
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Статус заказа</h2>
                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4">
                            <select name="status" class="select select-bordered w-full">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" 
                                            {{ $order->status === $status ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary w-full">
                                Обновить статус
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 pt-4 border-t space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-base-content/60">Создан:</span>
                            <span>{{ $order->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        @if($order->confirmed_at)
                            <div class="flex justify-between">
                                <span class="text-base-content/60">Подтверждён:</span>
                                <span>{{ $order->confirmed_at->format('d.m.Y H:i') }}</span>
                            </div>
                        @endif
                        @if($order->completed_at)
                            <div class="flex justify-between">
                                <span class="text-base-content/60">Выполнен:</span>
                                <span>{{ $order->completed_at->format('d.m.Y H:i') }}</span>
                            </div>
                        @endif
                        @if($order->cancelled_at)
                            <div class="flex justify-between">
                                <span class="text-base-content/60">Отменён:</span>
                                <span>{{ $order->cancelled_at->format('d.m.Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Информация о клиенте -->
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Клиент</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-base-content/60 mb-1">Имя</p>
                            <p class="font-medium">{{ $order->customer_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-base-content/60 mb-1">Телефон</p>
                            <a href="tel:{{ $order->customer_phone }}" class="link link-primary">
                                {{ $order->customer_phone }}
                            </a>
                        </div>
                        @if($order->customer_email)
                            <div>
                                <p class="text-xs text-base-content/60 mb-1">Email</p>
                                <a href="mailto:{{ $order->customer_email }}" class="link link-primary">
                                    {{ $order->customer_email }}
                                </a>
                            </div>
                        @endif
                        @if($order->delivery_address)
                            <div>
                                <p class="text-xs text-base-content/60 mb-1">Адрес доставки</p>
                                <p>{{ $order->delivery_address }}</p>
                            </div>
                        @endif
                        @if($order->comment)
                            <div>
                                <p class="text-xs text-base-content/60 mb-1">Комментарий</p>
                                <p class="text-sm">{{ $order->comment }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
