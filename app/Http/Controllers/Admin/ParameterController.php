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
        $ordersEnabled = Setting::get('orders_enabled', true);

        return view('admin.parameters.index', [
            'title' => 'Параметры',
            'ordersEnabled' => $ordersEnabled,
        ]);
    }

    public function update(UpdateParameterRequest $request): RedirectResponse
    {
        Setting::set('orders_enabled', $request->boolean('orders_enabled'));

        return redirect()
            ->route('admin.parameters.index')
            ->with('success', 'Параметры сохранены.');
    }
}
