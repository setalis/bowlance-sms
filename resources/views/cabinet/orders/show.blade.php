@extends('layouts.cabinet')

@section('content')
    <div class="mb-6">
        <a href="{{ route('cabinet.orders.index') }}" class="btn btn-ghost btn-sm mb-2">
            <span class="icon-[tabler--arrow-left] size-4"></span>
            Назад к списку
        </a>
        <h1 class="text-2xl font-bold">{{ $title }}</h1>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Состав заказа</h2>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex gap-4 rounded-lg bg-base-200/50 p-4">
                                <div class="flex-1">
                                    <div class="mb-2 flex items-start justify-between">
                                        <h3 class="font-medium">{{ $item->name }}</h3>
                                        <span class="badge badge-primary">{{ $item->item_type === 'dish' ? 'Блюдо' : 'Боул' }}</span>
                                    </div>
                                    @if($item->item_type === 'bowl' && $item->bowl_products)
                                        <div class="mt-2">
                                            <p class="mb-1 text-xs text-base-content/60">Состав боула:</p>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($item->bowl_products as $product)
                                                    <span class="badge badge-outline badge-sm">{{ $product['name'] }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    @if($item->calories || $item->proteins || $item->fats || $item->carbohydrates)
                                        <div class="mt-2 flex flex-wrap gap-2 text-xs">
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

                    <div class="mt-6 space-y-2 border-t pt-4">
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
                        <div class="flex justify-between border-t pt-2 text-lg font-bold">
                            <span>Итого:</span>
                            <span class="text-primary">{{ number_format($order->total, 2) }} ₾</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            @if($order->delivery_type?->value === 'delivery')
                <div class="card border-emerald-200 dark:border-emerald-800">
                    <div class="card-body">
                        <h2 class="card-title mb-4 flex items-center gap-2">
                            <span class="icon-[tabler--truck-delivery] size-5 text-emerald-600"></span>
                            Доставка Wolt
                        </h2>
                        @if($order->wolt_tracking_url)
                            <p class="text-sm text-base-content/70 mb-4">
                                Доставка создана в Wolt. Вы можете отслеживать статус и местоположение курьера по ссылке ниже.
                            </p>
                            <a href="{{ $order->wolt_tracking_url }}" target="_blank" rel="noopener" class="btn btn-primary w-full gap-2">
                                <span class="icon-[tabler--external-link] size-4"></span>
                                Открыть отслеживание в Wolt
                            </a>
                            @if($order->wolt_delivery_id)
                                <p class="mt-3 text-xs text-base-content/50">ID доставки: {{ $order->wolt_delivery_id }}</p>
                            @endif
                        @else
                            <p class="text-sm text-base-content/70">
                                Доставка по этому заказу в Wolt не была создана автоматически. С вами могут связаться для уточнения доставки.
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Статус заказа</h2>
                    <span class="badge badge-{{ $order->status->color() }} badge-lg">{{ $order->status->label() }}</span>
                    <div class="mt-4 space-y-2 border-t pt-4 text-sm">
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

            <div class="card">
                <div class="card-body">
                    <h2 class="card-title mb-4">Доставка</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="mb-1 text-xs text-base-content/60">Имя</p>
                            <p class="font-medium">{{ $order->customer_name }}</p>
                        </div>
                        <div>
                            <p class="mb-1 text-xs text-base-content/60">Телефон</p>
                            <a href="tel:{{ $order->customer_phone }}" class="link link-primary">{{ $order->customer_phone }}</a>
                        </div>
                        @if($order->customer_email)
                            <div>
                                <p class="mb-1 text-xs text-base-content/60">Email</p>
                                <a href="mailto:{{ $order->customer_email }}" class="link link-primary">{{ $order->customer_email }}</a>
                            </div>
                        @endif
                        <div>
                            <p class="mb-1 text-xs text-base-content/60">Способ получения</p>
                            <p>{{ $order->delivery_type?->label() ?? 'Доставка' }}</p>
                        </div>
                        @if($order->delivery_address && $order->delivery_type?->value === 'delivery')
                            <div>
                                <p class="mb-1 text-xs text-base-content/60">Адрес доставки</p>
                                <p>{{ $order->delivery_address }}</p>
                            </div>
                        @endif
                        @if($order->comment)
                            <div>
                                <p class="mb-1 text-xs text-base-content/60">Комментарий</p>
                                <p class="text-sm">{{ $order->comment }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
