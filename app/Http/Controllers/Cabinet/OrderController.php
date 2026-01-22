<?php

namespace App\Http\Controllers\Cabinet;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Response;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $orders = auth()->user()
            ->orders()
            ->with('items')
            ->latest()
            ->paginate(15);

        return view('cabinet.orders.index', [
            'title' => 'Мои заказы',
            'orders' => $orders,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function show(Order $order): View|Response
    {
        if ($order->user_id !== auth()->id()) {
            abort(404);
        }

        $order->load('items.dish');

        return view('cabinet.orders.show', [
            'title' => 'Заказ '.$order->order_number,
            'order' => $order,
            'statuses' => OrderStatus::cases(),
        ]);
    }
}
