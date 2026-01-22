@extends('layouts.cabinet')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">{{ $title }}</h1>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($orders->isEmpty())
                <div class="p-6 text-center text-base-content/60">
                    <span class="icon-[tabler--shopping-cart-off] size-16 mb-4 inline-block"></span>
                    <p>Заказов пока нет</p>
                    <a href="{{ route('home') }}" class="btn btn-primary btn-sm mt-4">Сделать заказ</a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Номер заказа</th>
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
                                        <a href="{{ route('cabinet.orders.show', $order) }}" class="link link-primary font-medium">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="font-bold">{{ number_format($order->total, 2) }} ₾</td>
                                    <td>
                                        <span class="badge badge-{{ $order->status->color() }}">
                                            {{ $order->status->label() }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('cabinet.orders.show', $order) }}" class="btn btn-sm btn-ghost" title="Просмотр">
                                            <span class="icon-[tabler--eye] size-4"></span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="border-t p-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
