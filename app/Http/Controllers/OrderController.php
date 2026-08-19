<?php

namespace App\Http\Controllers;

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Mail\NewOrderMail;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PhoneVerification;
use App\Models\Setting;
use App\Services\DiscountService;
use App\Services\PhoneAuthService;
use App\Services\PosterService;
use App\Services\WoltDriveService;
use App\Support\PhoneNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function __construct(
        protected PhoneAuthService $phoneAuthService,
        protected WoltDriveService $woltDriveService,
        protected PosterService $posterService,
        protected DiscountService $discountService
    ) {}

    public function store(StoreOrderRequest $request): JsonResponse
    {
        if (! Setting::get('orders_enabled', true)) {
            return response()->json([
                'success' => false,
                'message' => __('frontend.orders_disabled_message'),
            ], 503);
        }

        try {
            DB::beginTransaction();

            $isCallback = $request->verification_method === 'callback';
            $skipsPhoneVerification = $request->skipsPhoneVerification();

            $customerPhone = PhoneNumber::toE164($request->customer_phone);

            $verification = null;
            if (! $skipsPhoneVerification) {
                $verification = PhoneVerification::where('request_id', $request->verification_request_id)
                    ->where('verified', true)
                    ->first();
            }

            // Check if we need to re-authenticate (user switching scenario)
            $authResult = $this->phoneAuthService->shouldReauthenticate(
                auth()->id(),
                $customerPhone
            );

            // If requires confirmation and user hasn't confirmed yet
            if ($authResult['should_reauth'] && ! $request->boolean('confirm_switch_user')) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'requires_confirmation' => true,
                    'message' => 'Вы авторизованы как другой пользователь. Переключиться?',
                    'target_user' => [
                        'id' => $authResult['target_user']->id,
                        'name' => $authResult['target_user']->name,
                        'phone' => $authResult['target_user']->phone,
                    ],
                ], 409);
            }

            // Find or create user by phone
            $user = $this->phoneAuthService->findOrCreateUser(
                $customerPhone,
                $request->customer_email,
                $request->customer_name
            );

            // Authenticate the user
            $this->phoneAuthService->authenticateUser($user);

            $resolvedItems = collect($request->items)->map(function (array $item) {
                if ($item['type'] !== 'dish') {
                    return $item;
                }

                return $this->resolveDishOrderItem($item);
            });

            $subtotal = $resolvedItems->sum(fn ($item) => $item['price'] * $item['quantity']);

            $deliveryType = DeliveryType::from($request->delivery_type ?? DeliveryType::Delivery->value);
            $pricing = $this->discountService->calculateTotal((float) $subtotal, $deliveryType);
            $deliveryFee = $pricing['delivery_fee'];
            $total = $pricing['total'];

            $deliveryAddress = $request->delivery_address;
            if ($request->delivery_type === DeliveryType::Delivery->value && $request->filled('delivery_city') && $request->filled('delivery_street')) {
                $deliveryAddress = trim(implode(', ', array_filter([
                    $request->delivery_city,
                    trim($request->delivery_street.($request->filled('delivery_house') ? ' '.$request->delivery_house : '')),
                ])));
            }

            $order = Order::create([
                'user_id' => $user->id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $customerPhone,
                'customer_email' => $request->customer_email,
                'delivery_type' => $request->delivery_type ?? DeliveryType::Delivery->value,
                'delivery_time' => $request->delivery_time,
                'delivery_address' => $deliveryAddress,
                'delivery_city' => $request->delivery_city,
                'delivery_street' => $request->delivery_street,
                'delivery_house' => $request->delivery_house,
                'entrance' => $request->entrance,
                'floor' => $request->floor,
                'apartment' => $request->apartment,
                'intercom' => $request->intercom,
                'courier_comment' => $request->courier_comment,
                'receiver_phone' => $request->receiver_phone,
                'leave_at_door' => $request->boolean('leave_at_door', false),
                'comment' => $request->comment,
                'promo_code' => $request->filled('promo_code') ? trim($request->promo_code) : null,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'payment_method' => $request->payment_method ?? 'cash',
                'status' => OrderStatus::New,
                'phone_verified' => ! $skipsPhoneVerification,
                'phone_verified_at' => $skipsPhoneVerification ? null : ($verification?->verified_at ?? now()),
                'needs_callback' => $isCallback,
            ]);

            foreach ($resolvedItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_type' => $item['type'],
                    'dish_id' => $item['type'] === 'dish' ? $item['id'] : null,
                    'drink_id' => $item['type'] === 'drink' ? $item['id'] : null,
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['price'] * $item['quantity'],
                    'calories' => $item['calories'] ?? null,
                    'proteins' => $item['proteins'] ?? null,
                    'fats' => $item['fats'] ?? null,
                    'carbohydrates' => $item['carbs'] ?? null,
                    'bowl_products' => in_array($item['type'], ['bowl', 'breakfast'], true) ? ($item['products'] ?? []) : null,
                    'dish_addons' => $item['type'] === 'dish' ? ($item['addons'] ?? null) : null,
                ]);
            }

            // Автосохранение адреса для авторизованных пользователей
            if (auth()->check() && $request->delivery_type === DeliveryType::Delivery->value && $deliveryAddress) {
                $addressExists = auth()->user()->addresses()
                    ->where('address', $deliveryAddress)
                    ->exists();

                if (! $addressExists) {
                    $addressCount = auth()->user()->addresses()->count();
                    auth()->user()->addresses()->create([
                        'label' => 'Адрес '.($addressCount + 1),
                        'address' => $deliveryAddress,
                        'delivery_city' => $request->delivery_city,
                        'delivery_street' => $request->delivery_street,
                        'delivery_house' => $request->delivery_house,
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

            Mail::to(config('mail.admin_email'))->send(new NewOrderMail($order->load('items')));

            $this->posterService->createIncomingOrder($order->fresh(['items.dish', 'items.drink']));
            $this->woltDriveService->createDeliveryForOrder($order->fresh('items'));
            $order->refresh();

            return response()->json([
                'success' => true,
                'message' => 'Заказ успешно создан',
                'order' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'total' => $order->total,
                    'status' => $order->status->label(),
                    'delivery_type' => $order->delivery_type?->value,
                    'needs_callback' => $order->needs_callback,
                    'wolt_delivery_id' => $order->wolt_delivery_id,
                    'wolt_tracking_url' => $order->wolt_tracking_url,
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

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>
     */
    protected function resolveDishOrderItem(array $item): array
    {
        $dish = Dish::query()->with('addons')->find($item['id']);

        if (! $dish) {
            return $item;
        }

        $basePrice = (float) ($dish->discount_price ?? $dish->price);
        $addonsData = [];
        $addonsTotal = 0.0;
        $addonCalories = 0;
        $addonProteins = 0.0;
        $addonFats = 0.0;
        $addonCarbs = 0.0;

        foreach ($item['addons'] ?? [] as $addonInput) {
            $attached = $dish->addons->firstWhere('id', (int) ($addonInput['id'] ?? 0));

            if (! $attached) {
                continue;
            }

            $quantity = max(1, (int) ($addonInput['quantity'] ?? 1));
            $addonPrice = (float) ($attached->pivot->price ?? $attached->price);
            $addonsTotal += $addonPrice * $quantity;
            $addonCalories += (int) ($attached->calories ?? 0) * $quantity;
            $addonProteins += (float) ($attached->proteins ?? 0) * $quantity;
            $addonFats += (float) ($attached->fats ?? 0) * $quantity;
            $addonCarbs += (float) ($attached->carbohydrates ?? 0) * $quantity;

            $addonsData[] = [
                'id' => $attached->id,
                'name' => $attached->name,
                'price' => $addonPrice,
                'quantity' => $quantity,
                'calories' => $attached->calories,
                'proteins' => $attached->proteins,
                'fats' => $attached->fats,
                'carbs' => $attached->carbohydrates,
            ];
        }

        $item['price'] = round($basePrice + $addonsTotal, 2);
        $item['addons'] = $addonsData !== [] ? $addonsData : null;
        $item['name'] = $dish->name;
        $item['calories'] = (int) ($dish->calories ?? 0) + $addonCalories;
        $item['proteins'] = (float) ($dish->proteins ?? 0) + $addonProteins;
        $item['fats'] = (float) ($dish->fats ?? 0) + $addonFats;
        $item['carbs'] = (float) ($dish->carbohydrates ?? 0) + $addonCarbs;

        return $item;
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
