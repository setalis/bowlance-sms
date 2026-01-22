@extends('layouts.cabinet')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ $title }}</h1>
        <p class="text-base-content/80 mt-1">Добро пожаловать, {{ auth()->user()->name }}</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title mb-4">
                    <span class="icon-[tabler--shopping-cart] size-5"></span>
                    Последние заказы
                </h2>
                @if($recentOrders->isEmpty())
                    <p class="text-base-content/60">У вас пока нет заказов.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary btn-sm mt-4">Сделать заказ</a>
                @else
                    <div class="space-y-3">
                        @foreach($recentOrders as $order)
                            <a href="{{ route('cabinet.orders.show', $order) }}" class="flex items-center justify-between rounded-lg border border-base-content/10 p-3 transition hover:bg-base-200/50">
                                <div>
                                    <span class="font-medium">{{ $order->order_number }}</span>
                                    <span class="badge badge-{{ $order->status->color() }} badge-sm ms-2">{{ $order->status->label() }}</span>
                                </div>
                                <span class="font-bold">{{ number_format($order->total, 2) }} ₾</span>
                            </a>
                        @endforeach
                    </div>
                    <a href="{{ route('cabinet.orders.index') }}" class="btn btn-ghost btn-sm mt-4">Все заказы</a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="card-title mb-4">
                    <span class="icon-[tabler--user] size-5"></span>
                    Мой профиль
                </h2>
                <p class="text-base-content/80 mb-4">Измените имя, email и телефон.</p>
                <a href="{{ route('cabinet.profile.edit') }}" class="btn btn-primary btn-sm">Редактировать данные</a>
            </div>
        </div>
    </div>
@endsection
