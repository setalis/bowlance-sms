<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiscountRequest;
use App\Http\Requests\UpdateDiscountRequest;
use App\Models\Discount;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DiscountController extends Controller
{
    public function index(): View
    {
        $discounts = Discount::query()->orderBy('scope')->orderByDesc('created_at')->paginate(15);

        return view('admin.discounts.index', [
            'title' => 'Скидки и акции',
            'discounts' => $discounts,
        ]);
    }

    public function create(): View
    {
        return view('admin.discounts.create', [
            'title' => 'Создать скидку',
        ]);
    }

    public function store(StoreDiscountRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['scope'] = $data['scope'] ?? 'pickup';

        Discount::create($data);

        return redirect()
            ->route('admin.discounts.index')
            ->with('success', 'Скидка успешно создана.');
    }

    public function edit(Discount $discount): View
    {
        return view('admin.discounts.edit', [
            'title' => 'Редактировать скидку',
            'discount' => $discount,
        ]);
    }

    public function update(UpdateDiscountRequest $request, Discount $discount): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['scope'] = $data['scope'] ?? 'pickup';

        $discount->update($data);

        return redirect()
            ->route('admin.discounts.index')
            ->with('success', 'Скидка успешно обновлена.');
    }

    public function destroy(Discount $discount): RedirectResponse
    {
        $discount->delete();

        return redirect()
            ->route('admin.discounts.index')
            ->with('success', 'Скидка удалена.');
    }
}
