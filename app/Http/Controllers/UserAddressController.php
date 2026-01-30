<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserAddressRequest;
use App\Models\UserAddress;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class UserAddressController extends Controller
{
    use AuthorizesRequests;

    public function index(): JsonResponse
    {
        $addresses = auth()->user()->addresses()->latest()->get();

        return response()->json([
            'success' => true,
            'addresses' => $addresses,
        ]);
    }

    public function store(StoreUserAddressRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $isFirstAddress = auth()->user()->addresses()->count() === 0;
            $isDefault = $request->boolean('is_default', $isFirstAddress);

            // Если устанавливаем новый адрес по умолчанию, сбрасываем старый
            if ($isDefault) {
                auth()->user()->addresses()->update(['is_default' => false]);
            }

            $address = auth()->user()->addresses()->create([
                'label' => $request->label,
                'address' => $request->address,
                'is_default' => $isDefault,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Адрес успешно добавлен',
                'address' => $address,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при добавлении адреса: '.$e->getMessage(),
            ], 500);
        }
    }

    public function update(StoreUserAddressRequest $request, UserAddress $address): JsonResponse
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
                'is_default' => $request->boolean('is_default', $address->is_default),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Адрес успешно обновлен',
                'address' => $address,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении адреса: '.$e->getMessage(),
            ], 500);
        }
    }

    public function destroy(UserAddress $address): JsonResponse
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

            return response()->json([
                'success' => true,
                'message' => 'Адрес успешно удален',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении адреса: '.$e->getMessage(),
            ], 500);
        }
    }

    public function setDefault(UserAddress $address): JsonResponse
    {
        $this->authorize('update', $address);

        try {
            DB::beginTransaction();

            // Сбросить все дефолтные адреса пользователя
            auth()->user()->addresses()->update(['is_default' => false]);

            // Установить текущий как дефолтный
            $address->update(['is_default' => true]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Адрес установлен по умолчанию',
                'address' => $address,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при установке адреса по умолчанию: '.$e->getMessage(),
            ], 500);
        }
    }
}
