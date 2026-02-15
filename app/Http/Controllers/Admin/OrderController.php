<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\ConstructorProduct;
use App\Models\Discount;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with('items')->latest();

        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Поиск по номеру заказа или имени клиента
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(20);

        return view('admin.orders.index', [
            'title' => 'Заказы',
            'orders' => $orders,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function show(Order $order): View
    {
        $order->load('items.dish', 'user');

        return view('admin.orders.show', [
            'title' => 'Заказ '.$order->order_number,
            'order' => $order,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:new,unconfirmed,in_progress,completed,cancelled',
        ]);

        $newStatus = OrderStatus::from($request->status);
        $order->status = $newStatus;

        // Обновляем временные метки в зависимости от статуса
        match ($newStatus) {
            OrderStatus::InProgress => $order->confirmed_at = now(),
            OrderStatus::Completed => $order->completed_at = now(),
            OrderStatus::Cancelled => $order->cancelled_at = now(),
            default => null,
        };

        $order->save();

        return redirect()->back()->with('success', 'Статус заказа обновлён');
    }

    public function create(): View
    {
        $dishes = Dish::with('category')->orderBy('name')->get();
        $constructorCategories = \App\Models\ConstructorCategory::with('products')->orderBy('sort_order')->get();

        return view('admin.orders.create', [
            'title' => 'Создать заказ',
            'dishes' => $dishes,
            'constructorCategories' => $constructorCategories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'delivery_type' => 'required|in:delivery,pickup',
            'delivery_address' => 'required_if:delivery_type,delivery|nullable|string|max:1000',
            'comment' => 'nullable|string|max:1000',
            'status' => 'required|in:new,unconfirmed,in_progress,completed,cancelled',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:dish,bowl',
            'items.*.dish_id' => 'nullable|exists:dishes,id',
            'items.*.bowl_products' => 'nullable|array',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Получаем блюда
            $dishIds = collect($validated['items'])->where('type', 'dish')->pluck('dish_id')->filter();
            $dishes = Dish::whereIn('id', $dishIds)->get()->keyBy('id');

            // Получаем продукты конструктора для расчёта цен боулов
            $allProductIds = collect($validated['items'])
                ->where('type', 'bowl')
                ->pluck('bowl_products')
                ->flatten(1)
                ->filter()
                ->unique();
            $constructorProducts = ConstructorProduct::whereIn('id', $allProductIds)->get()->keyBy('id');

            // Подсчёт итоговой суммы
            $subtotal = collect($validated['items'])->sum(function ($item) use ($dishes, $constructorProducts) {
                if ($item['type'] === 'dish') {
                    $dish = $dishes->get($item['dish_id']);
                    $price = $dish->discount_price ?? $dish->price;
                } else {
                    // Для боула считаем сумму всех продуктов
                    $price = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts) {
                        $constructorProduct = $constructorProducts->get($productId);

                        return $constructorProduct ? $constructorProduct->price : 0;
                    });
                }

                return $price * $item['quantity'];
            });

            $deliveryFee = 0;
            $total = $subtotal + $deliveryFee;

            if ($validated['delivery_type'] === 'pickup') {
                $pickupDiscount = Discount::forPickup()->first();
                if ($pickupDiscount) {
                    $discountAmount = $pickupDiscount->calculateDiscountAmount((float) $subtotal);
                    $total = max(0, round($subtotal - $discountAmount + $deliveryFee, 2));
                }
            }

            // Создание заказа
            $order = Order::create([
                'user_id' => auth()->id(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'],
                'delivery_type' => $validated['delivery_type'],
                'delivery_address' => $validated['delivery_address'],
                'comment' => $validated['comment'],
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'status' => OrderStatus::from($validated['status']),
            ]);

            // Создание позиций заказа
            foreach ($validated['items'] as $item) {
                if ($item['type'] === 'dish') {
                    $dish = $dishes->get($item['dish_id']);
                    $price = $dish->discount_price ?? $dish->price;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'item_type' => 'dish',
                        'dish_id' => $dish->id,
                        'name' => $dish->name,
                        'price' => $price,
                        'quantity' => $item['quantity'],
                        'subtotal' => $price * $item['quantity'],
                        'calories' => $dish->calories,
                        'proteins' => $dish->proteins,
                        'fats' => $dish->fats,
                        'carbohydrates' => $dish->carbohydrates,
                    ]);
                } else {
                    // Создаём боул
                    $bowlPrice = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts) {
                        $constructorProduct = $constructorProducts->get($productId);

                        return $constructorProduct ? $constructorProduct->price : 0;
                    });

                    $bowlCalories = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts) {
                        $constructorProduct = $constructorProducts->get($productId);

                        return $constructorProduct ? $constructorProduct->calories : 0;
                    });

                    $bowlProteins = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts) {
                        $constructorProduct = $constructorProducts->get($productId);

                        return $constructorProduct ? $constructorProduct->proteins : 0;
                    });

                    $bowlFats = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts) {
                        $constructorProduct = $constructorProducts->get($productId);

                        return $constructorProduct ? $constructorProduct->fats : 0;
                    });

                    $bowlCarbs = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts) {
                        $constructorProduct = $constructorProducts->get($productId);

                        return $constructorProduct ? $constructorProduct->carbohydrates : 0;
                    });

                    // Формируем массив с полной информацией о продуктах
                    $bowlProductsData = collect($item['bowl_products'])->map(function ($productId) use ($constructorProducts) {
                        $product = $constructorProducts->get($productId);

                        return $product ? [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => $product->price,
                        ] : null;
                    })->filter()->values()->toArray();

                    OrderItem::create([
                        'order_id' => $order->id,
                        'item_type' => 'bowl',
                        'dish_id' => null,
                        'name' => 'Собранный боул',
                        'price' => $bowlPrice,
                        'quantity' => $item['quantity'],
                        'subtotal' => $bowlPrice * $item['quantity'],
                        'calories' => $bowlCalories,
                        'proteins' => $bowlProteins,
                        'fats' => $bowlFats,
                        'carbohydrates' => $bowlCarbs,
                        'bowl_products' => $bowlProductsData,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Заказ успешно создан');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Ошибка при создании заказа: '.$e->getMessage());
        }
    }

    public function edit(Order $order): View
    {
        $order->load('items.dish');
        $dishes = Dish::with('category')->orderBy('name')->get();
        $constructorCategories = \App\Models\ConstructorCategory::with('products')->orderBy('sort_order')->get();

        return view('admin.orders.edit', [
            'title' => 'Редактировать заказ '.$order->order_number,
            'order' => $order,
            'dishes' => $dishes,
            'constructorCategories' => $constructorCategories,
            'statuses' => OrderStatus::cases(),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'delivery_type' => 'required|in:delivery,pickup',
            'delivery_address' => 'required_if:delivery_type,delivery|nullable|string|max:1000',
            'comment' => 'nullable|string|max:1000',
            'status' => 'required|in:new,unconfirmed,in_progress,completed,cancelled',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:dish,bowl',
            'items.*.dish_id' => 'nullable|exists:dishes,id',
            'items.*.bowl_products' => 'nullable|array',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // Получаем блюда
            $dishIds = collect($validated['items'])->where('type', 'dish')->pluck('dish_id')->filter();
            $dishes = Dish::whereIn('id', $dishIds)->get()->keyBy('id');

            // Получаем продукты конструктора для расчёта цен боулов
            $allProductIds = collect($validated['items'])
                ->where('type', 'bowl')
                ->pluck('bowl_products')
                ->flatten(1)
                ->filter()
                ->unique();
            $constructorProducts = ConstructorProduct::whereIn('id', $allProductIds)->get()->keyBy('id');

            // Подсчёт итоговой суммы
            $subtotal = collect($validated['items'])->sum(function ($item) use ($dishes, $constructorProducts) {
                if ($item['type'] === 'dish') {
                    $dish = $dishes->get($item['dish_id']);
                    $price = $dish->discount_price ?? $dish->price;
                } else {
                    // Для боула считаем сумму всех продуктов
                    $price = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts) {
                        $constructorProduct = $constructorProducts->get($productId);

                        return $constructorProduct ? $constructorProduct->price : 0;
                    });
                }

                return $price * $item['quantity'];
            });

            $deliveryFee = $order->delivery_fee;
            $total = $subtotal + $deliveryFee;

            if ($validated['delivery_type'] === 'pickup') {
                $pickupDiscount = Discount::forPickup()->first();
                if ($pickupDiscount) {
                    $discountAmount = $pickupDiscount->calculateDiscountAmount((float) $subtotal);
                    $total = max(0, round($subtotal - $discountAmount + $deliveryFee, 2));
                }
            }

            // Обновление заказа
            $newStatus = OrderStatus::from($validated['status']);
            $order->update([
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_email' => $validated['customer_email'],
                'delivery_type' => $validated['delivery_type'],
                'delivery_address' => $validated['delivery_address'],
                'comment' => $validated['comment'],
                'subtotal' => $subtotal,
                'total' => $total,
                'status' => $newStatus,
            ]);

            // Обновляем временные метки в зависимости от статуса
            if ($newStatus === OrderStatus::InProgress && ! $order->confirmed_at) {
                $order->confirmed_at = now();
            } elseif ($newStatus === OrderStatus::Completed && ! $order->completed_at) {
                $order->completed_at = now();
            } elseif ($newStatus === OrderStatus::Cancelled && ! $order->cancelled_at) {
                $order->cancelled_at = now();
            }
            $order->save();

            // Удаляем старые позиции
            $order->items()->delete();

            // Создание новых позиций заказа
            foreach ($validated['items'] as $item) {
                if ($item['type'] === 'dish') {
                    $dish = $dishes->get($item['dish_id']);
                    $price = $dish->discount_price ?? $dish->price;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'item_type' => 'dish',
                        'dish_id' => $dish->id,
                        'name' => $dish->name,
                        'price' => $price,
                        'quantity' => $item['quantity'],
                        'subtotal' => $price * $item['quantity'],
                        'calories' => $dish->calories,
                        'proteins' => $dish->proteins,
                        'fats' => $dish->fats,
                        'carbohydrates' => $dish->carbohydrates,
                    ]);
                } else {
                    // Создаём боул
                    $bowlPrice = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts) {
                        $constructorProduct = $constructorProducts->get($productId);

                        return $constructorProduct ? $constructorProduct->price : 0;
                    });

                    $bowlCalories = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts) {
                        $constructorProduct = $constructorProducts->get($productId);

                        return $constructorProduct ? $constructorProduct->calories : 0;
                    });

                    $bowlProteins = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts) {
                        $constructorProduct = $constructorProducts->get($productId);

                        return $constructorProduct ? $constructorProduct->proteins : 0;
                    });

                    $bowlFats = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts) {
                        $constructorProduct = $constructorProducts->get($productId);

                        return $constructorProduct ? $constructorProduct->fats : 0;
                    });

                    $bowlCarbs = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts) {
                        $constructorProduct = $constructorProducts->get($productId);

                        return $constructorProduct ? $constructorProduct->carbohydrates : 0;
                    });

                    // Формируем массив с полной информацией о продуктах
                    $bowlProductsData = collect($item['bowl_products'])->map(function ($productId) use ($constructorProducts) {
                        $product = $constructorProducts->get($productId);

                        return $product ? [
                            'id' => $product->id,
                            'name' => $product->name,
                            'price' => $product->price,
                        ] : null;
                    })->filter()->values()->toArray();

                    OrderItem::create([
                        'order_id' => $order->id,
                        'item_type' => 'bowl',
                        'dish_id' => null,
                        'name' => 'Собранный боул',
                        'price' => $bowlPrice,
                        'quantity' => $item['quantity'],
                        'subtotal' => $bowlPrice * $item['quantity'],
                        'calories' => $bowlCalories,
                        'proteins' => $bowlProteins,
                        'fats' => $bowlFats,
                        'carbohydrates' => $bowlCarbs,
                        'bowl_products' => $bowlProductsData,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Заказ успешно обновлён');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Ошибка при обновлении заказа: '.$e->getMessage());
        }
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Заказ удалён');
    }
}
