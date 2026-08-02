<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\ConstructorProduct;
use App\Models\Discount;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Support\PhoneNumber;
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
        $constructorCategories = \App\Models\ConstructorCategory::with('products.variants')->orderBy('sort_order')->get();

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
            'items.*.type' => 'required|in:dish,bowl,breakfast',
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
                ->whereIn('type', $this->constructorItemTypes())
                ->pluck('bowl_products')
                ->flatten(1)
                ->filter()
                ->unique();
            $constructorProducts = ConstructorProduct::with('variants')->whereIn('id', $allProductIds)->get()->keyBy('id');

            // Подсчёт итоговой суммы
            $subtotal = collect($validated['items'])->sum(function ($item) use ($dishes, $constructorProducts) {
                if ($item['type'] === 'dish') {
                    $dish = $dishes->get($item['dish_id']);
                    $price = $dish->discount_price ?? $dish->price;
                } elseif ($this->isConstructorItem($item['type'])) {
                    $price = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts, $item) {
                        return $this->constructorProductPrice($constructorProducts->get($productId), $item['type']);
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
                'customer_phone' => PhoneNumber::toE164($validated['customer_phone']),
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
                } elseif ($this->isConstructorItem($item['type'])) {
                    $this->createConstructorOrderItem($order, $item, $constructorProducts);
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
        $constructorCategories = \App\Models\ConstructorCategory::with('products.variants')->orderBy('sort_order')->get();

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
            'items.*.type' => 'required|in:dish,bowl,breakfast',
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
                ->whereIn('type', $this->constructorItemTypes())
                ->pluck('bowl_products')
                ->flatten(1)
                ->filter()
                ->unique();
            $constructorProducts = ConstructorProduct::with('variants')->whereIn('id', $allProductIds)->get()->keyBy('id');

            // Подсчёт итоговой суммы
            $subtotal = collect($validated['items'])->sum(function ($item) use ($dishes, $constructorProducts) {
                if ($item['type'] === 'dish') {
                    $dish = $dishes->get($item['dish_id']);
                    $price = $dish->discount_price ?? $dish->price;
                } elseif ($this->isConstructorItem($item['type'])) {
                    $price = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts, $item) {
                        return $this->constructorProductPrice($constructorProducts->get($productId), $item['type']);
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
                'customer_phone' => PhoneNumber::toE164($validated['customer_phone']),
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
                } elseif ($this->isConstructorItem($item['type'])) {
                    $this->createConstructorOrderItem($order, $item, $constructorProducts);
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

    /**
     * @return list<string>
     */
    protected function constructorItemTypes(): array
    {
        return ['bowl', 'breakfast'];
    }

    protected function isConstructorItem(string $type): bool
    {
        return in_array($type, $this->constructorItemTypes(), true);
    }

    protected function constructorItemName(string $type): string
    {
        return match ($type) {
            'breakfast' => 'Собранный завтрак',
            default => 'Собранный боул',
        };
    }

    /**
     * @param  \Illuminate\Support\Collection<int, ConstructorProduct>  $constructorProducts
     */
    protected function createConstructorOrderItem(Order $order, array $item, $constructorProducts): void
    {
        $type = $item['type'];

        $bowlPrice = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts, $type) {
            return $this->constructorProductPrice($constructorProducts->get($productId), $type);
        });

        $bowlCalories = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts, $type) {
            return $this->constructorProductAttribute($constructorProducts->get($productId), $type, 'calories');
        });

        $bowlProteins = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts, $type) {
            return $this->constructorProductAttribute($constructorProducts->get($productId), $type, 'proteins');
        });

        $bowlFats = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts, $type) {
            return $this->constructorProductAttribute($constructorProducts->get($productId), $type, 'fats');
        });

        $bowlCarbs = collect($item['bowl_products'])->sum(function ($productId) use ($constructorProducts, $type) {
            return $this->constructorProductAttribute($constructorProducts->get($productId), $type, 'carbohydrates');
        });

        $bowlProductsData = collect($item['bowl_products'])->map(function ($productId) use ($constructorProducts, $type) {
            $product = $constructorProducts->get($productId);

            if (! $product) {
                return null;
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $this->constructorProductPrice($product, $type),
            ];
        })->filter()->values()->toArray();

        OrderItem::create([
            'order_id' => $order->id,
            'item_type' => $item['type'],
            'dish_id' => null,
            'name' => $this->constructorItemName($item['type']),
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

    protected function constructorProductPrice(?ConstructorProduct $product, string $type): float
    {
        if (! $product) {
            return 0;
        }

        return (float) ($product->variantFor($type)?->price ?? 0);
    }

    protected function constructorProductAttribute(?ConstructorProduct $product, string $type, string $attribute): float|int
    {
        if (! $product) {
            return 0;
        }

        return $product->variantFor($type)?->{$attribute} ?? 0;
    }
}
