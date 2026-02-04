<?php

namespace App\Http\Controllers\Cabinet;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserAddressRequest;
use App\Models\UserAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function index(): View
    {
        $addresses = auth()->user()->addresses()->latest()->get();

        return view('cabinet.addresses.index', [
            'title' => 'Мои адреса',
            'addresses' => $addresses,
        ]);
    }

    public function create(): View
    {
        return view('cabinet.addresses.create', [
            'title' => 'Добавить адрес',
        ]);
    }

    public function store(StoreUserAddressRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $isFirstAddress = auth()->user()->addresses()->count() === 0;
            $isDefault = $request->boolean('is_default', $isFirstAddress);

            // Если устанавливаем новый адрес по умолчанию, сбрасываем старый
            if ($isDefault) {
                auth()->user()->addresses()->update(['is_default' => false]);
            }

            auth()->user()->addresses()->create([
                'label' => $request->label,
                'address' => $request->address,
                'entrance' => $request->entrance,
                'floor' => $request->floor,
                'apartment' => $request->apartment,
                'intercom' => $request->intercom,
                'courier_comment' => $request->courier_comment,
                'receiver_phone' => $request->receiver_phone,
                'leave_at_door' => $request->boolean('leave_at_door', false),
                'is_default' => $isDefault,
            ]);

            DB::commit();

            return redirect()->route('cabinet.addresses.index')
                ->with('success', 'Адрес успешно добавлен');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Ошибка при добавлении адреса: '.$e->getMessage());
        }
    }

    public function edit(UserAddress $address): View
    {
        $this->authorize('update', $address);

        return view('cabinet.addresses.edit', [
            'title' => 'Редактировать адрес',
            'address' => $address,
        ]);
    }

    public function update(StoreUserAddressRequest $request, UserAddress $address): RedirectResponse
    {
        $this->authorize('update', $address);

        try {
            DB::beginTransaction();

            // Если устанавливаем этот адрес по умолчанию, сбрасываем старый
            if ($request->boolean('is_default')) {
                auth()->user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            }

            $address->update([
                'label' => $request->label,
                'address' => $request->address,
                'entrance' => $request->entrance,
                'floor' => $request->floor,
                'apartment' => $request->apartment,
                'intercom' => $request->intercom,
                'courier_comment' => $request->courier_comment,
                'receiver_phone' => $request->receiver_phone,
                'leave_at_door' => $request->boolean('leave_at_door', false),
                'is_default' => $request->boolean('is_default', $address->is_default),
            ]);

            DB::commit();

            return redirect()->route('cabinet.addresses.index')
                ->with('success', 'Адрес успешно обновлен');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении адреса: '.$e->getMessage());
        }
    }

    public function destroy(UserAddress $address): RedirectResponse
    {
        $this->authorize('delete', $address);

        try {
            $wasDefault = $address->is_default;
            $address->delete();

            // Если удалили дефолтный адрес, установить первый оставшийся как дефолтный
            if ($wasDefault) {
                $firstAddress = auth()->user()->addresses()->first();
                if ($firstAddress) {
                    $firstAddress->update(['is_default' => true]);
                }
            }

            return redirect()->route('cabinet.addresses.index')
                ->with('success', 'Адрес успешно удален');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Ошибка при удалении адреса: '.$e->getMessage());
        }
    }

    public function setDefault(UserAddress $address): RedirectResponse
    {
        $this->authorize('update', $address);

        try {
            DB::beginTransaction();

            // Сбросить все дефолтные адреса пользователя
            auth()->user()->addresses()->update(['is_default' => false]);

            // Установить текущий как дефолтный
            $address->update(['is_default' => true]);

            DB::commit();

            return redirect()->route('cabinet.addresses.index')
                ->with('success', 'Адрес установлен по умолчанию');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Ошибка при установке адреса по умолчанию: '.$e->getMessage());
        }
    }
}
