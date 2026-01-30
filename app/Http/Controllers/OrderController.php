<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PhoneVerification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $verification = PhoneVerification::where('request_id', $request->verification_request_id)
                ->where('verified', true)
                ->first();

            $subtotal = collect($request->items)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            $deliveryFee = 0;
            $total = $subtotal + $deliveryFee;

            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'delivery_type' => $request->delivery_type ?? 'delivery',
                'delivery_address' => $request->delivery_address,
                'entrance' => $request->entrance,
                'floor' => $request->floor,
                'apartment' => $request->apartment,
                'intercom' => $request->intercom,
                'courier_comment' => $request->courier_comment,
                'receiver_phone' => $request->receiver_phone,
                'leave_at_door' => $request->boolean('leave_at_door', false),
                'comment' => $request->comment,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'status' => OrderStatus::New,
                'phone_verified' => true,
                'phone_verified_at' => $verification?->verified_at ?? now(),
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_type' => $item['type'],
                    'dish_id' => $item['type'] === 'dish' ? $item['id'] : null,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'calories' => $item['calories'] ?? null,
                    'proteins' => $item['proteins'] ?? null,
                    'fats' => $item['fats'] ?? null,
                    'carbohydrates' => $item['carbs'] ?? null,
                    'bowl_products' => $item['type'] === 'bowl' ? ($item['products'] ?? []) : null,
                ]);
            }

            // Автосохранение адреса для авторизованных пользователей
            if (auth()->check() && $request->delivery_type === 'delivery' && $request->delivery_address) {
                $addressExists = auth()->user()->addresses()
                    ->where('address', $request->delivery_address)
                    ->exists();

                if (! $addressExists) {
                    $addressCount = auth()->user()->addresses()->count();
                    auth()->user()->addresses()->create([
                        'label' => 'Адрес '.($addressCount + 1),
                        'address' => $request->delivery_address,
                        'entrance' => $request->entrance,
                        'floor' => $request->floor,
                        'apartment' => $request->apartment,
                        'intercom' => $request->intercom,
                        'courier_comment' => $request->courier_comment,
                        'receiver_phone' => $request->receiver_phone,
                        'leave_at_door' => $request->boolean('leave_at_door', false),
                        'is_default' => $addressCount === 0,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Заказ успешно создан',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => $order->total,
                    'status' => $order->status->label(),
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании заказа: '.$e->getMessage(),
            ], 500);
        }
    }

    public function show(Order $order): JsonResponse
    {
        $order->load('items');

        return response()->json([
            'success' => true,
            'order' => $order,
        ]);
    }
}
