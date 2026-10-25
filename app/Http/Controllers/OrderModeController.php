<?php

namespace App\Http\Controllers;

use App\Enums\OrderMode;
use App\Http\Requests\StoreOrderModeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrderModeController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (OrderMode::current() !== null) {
            return redirect()->route('home');
        }

        return view('frontend.order-mode', [
            'title' => __('frontend.order_mode_title'),
        ]);
    }

    public function store(StoreOrderModeRequest $request): RedirectResponse
    {
        OrderMode::put(OrderMode::from($request->validated('order_mode')));

        return redirect()->route('home');
    }

    public function qrOnsite(): RedirectResponse
    {
        OrderMode::put(OrderMode::Onsite);

        return redirect()->route('home');
    }
}
