<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateParameterRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ParameterController extends Controller
{
    public function index(): View
    {
        return view('admin.parameters.index', [
            'title' => 'Параметры',
            'ordersEnabled' => Setting::get('orders_enabled', true),
            'phoneVerificationEnabled' => Setting::get('phone_verification_enabled', true),
            'woltDeliveryEnabled' => Setting::get('wolt_delivery_enabled', true),
        ]);
    }

    public function update(UpdateParameterRequest $request): RedirectResponse
    {
        Setting::set('orders_enabled', $request->boolean('orders_enabled'));
        Setting::set('phone_verification_enabled', $request->boolean('phone_verification_enabled'));
        Setting::set('wolt_delivery_enabled', $request->boolean('wolt_delivery_enabled'));

        return redirect()
            ->route('admin.parameters.index')
            ->with('success', 'Параметры сохранены.');
    }
}
