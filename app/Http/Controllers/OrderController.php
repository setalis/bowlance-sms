<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'delivery_address' => 'nullable|string|max:1000',
            'comment' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:dish,bowl',
            'items.*.id' => 'required|integer',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.calories' => 'nullable|integer',
            'items.*.proteins' => 'nullable|numeric',
            'items.*.fats' => 'nullable|numeric',
            'items.*.carbs' => 'nullable|numeric',
            'items.*.products' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Подсчёт итоговой суммы
            $subtotal = collect($request->items)->sum(function ($item) {
                return $item['price'] * $item['quantity'];
            });

            $deliveryFee = 0; // Можно добавить логику расчёта доставки
            $total = $subtotal + $deliveryFee;

            // Создание заказа
            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'delivery_address' => $request->delivery_address,
                'comment' => $request->comment,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'status' => OrderStatus::New,
            ]);

            // Создание позиций заказа
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
