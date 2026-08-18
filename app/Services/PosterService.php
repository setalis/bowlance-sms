<?php

namespace App\Services;

use App\Enums\DeliveryType;
use App\Enums\DiscountScope;
use App\Enums\DiscountType;
use App\Models\ConstructorProduct;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Support\PhoneNumber;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PosterService
{
    /**
     * Poster: "invalid field: Client phone" — номера не существует.
     */
    private const ERROR_INVALID_CLIENT_PHONE = 155;

    public function __construct(protected DiscountService $discountService) {}

    public function isEnabled(): bool
    {
        return (bool) config('poster.enabled') && filled(config('poster.token'));
    }

    public function createIncomingOrder(Order $order): ?array
    {
        if (! $this->isEnabled()) {
            Log::info('Poster: integration disabled or token missing, order skipped', [
                'order_id' => $order->id,
                'enabled' => (bool) config('poster.enabled'),
                'token_set' => filled(config('poster.token')),
            ]);

            return null;
        }

        $order->loadMissing(['items.dish.addons', 'items.drink']);

        $products = $order->items
            ->filter(fn ($item) => $item->item_type === 'dish' && $item->dish?->poster_product_id)
            ->map(fn ($item) => $this->buildDishProduct($item))
            ->values()
            ->all();

        foreach ($order->items->where('item_type', 'drink') as $drinkItem) {
            $drinkProduct = $this->buildDrinkProduct($drinkItem);

            if ($drinkProduct !== null) {
                $products[] = $drinkProduct;
            }
        }

        foreach ($order->items->whereIn('item_type', ['bowl', 'breakfast']) as $bowlItem) {
            $bowlProduct = $this->buildBowlProduct($bowlItem);

            if ($bowlProduct !== null) {
                $products[] = $bowlProduct;
            }
        }

        if (empty($products)) {
            Log::warning('Poster: no order items mapped to Poster products, order skipped', [
                'order_id' => $order->id,
                'constructor_product_id' => config('poster.constructor_product_id'),
                'breakfast_constructor_product_id' => config('poster.breakfast_constructor_product_id'),
                'items' => $order->items->map(fn ($item) => [
                    'item_type' => $item->item_type,
                    'dish_id' => $item->dish_id,
                    'drink_id' => $item->drink_id,
                    'poster_product_id' => $item->dish?->poster_product_id ?? $item->drink?->poster_product_id,
                    'bowl_product_ids' => collect($item->bowl_products ?? [])->pluck('id')->all(),
                    'dish_addon_ids' => collect($item->dish_addons ?? [])->pluck('id')->all(),
                ])->all(),
            ]);

            return null;
        }

        // Как в документации Poster + sendRequest($url, 'post', $incoming_order):
        // https://dev.joinposter.com/docs/v3/web/incomingOrders/createIncomingOrder
        // POST application/x-www-form-urlencoded (http_build_query), phone — строка.
        $phone = PhoneNumber::toE164($order->customer_phone);

        if ($phone === '') {
            Log::error('Poster: order phone is not a valid number, order skipped', [
                'order_id' => $order->id,
                'customer_phone' => $order->customer_phone,
            ]);

            return null;
        }

        $incomingOrder = [
            'spot_id' => (int) config('poster.spot_id'),
            'phone' => $phone,
            'first_name' => $order->customer_name,
            'address' => $order->delivery_address,
            'comment' => $this->buildOrderComment($order),
            'service_mode' => $this->posterServiceMode($order),
            'products' => $products,
        ];

        $deliveryPrice = $this->posterDeliveryPrice($order);

        if ($deliveryPrice !== null) {
            $incomingOrder['delivery_price'] = $deliveryPrice;
        }

        $deliveryTime = $this->posterDeliveryTime($order);

        if ($deliveryTime !== null) {
            $incomingOrder['delivery_time'] = $deliveryTime;
        }

        $url = 'https://joinposter.com/api/incomingOrders.createIncomingOrder'
            .'?token='.config('poster.token');

        Log::info('Poster: sending incoming order', [
            'order_id' => $order->id,
            'phone' => $phone,
            'content_type' => 'application/x-www-form-urlencoded',
            'payload' => $incomingOrder,
            'products' => $products,
        ]);

        try {
            // Как sendRequest($url, 'post', $params): http_build_query + User-Agent Poster
            $response = Http::withUserAgent('Poster (http://joinposter.com)')
                ->asForm()
                ->post($url, $incomingOrder);

            if ($response->failed()) {
                Log::error('Poster API error', [
                    'order_id' => $order->id,
                    'status' => $response->status(),
                    'phone' => $phone,
                    'body' => $response->body(),
                ]);

                return null;
            }

            $json = $response->json();

            // Poster возвращает ошибки с HTTP 200 и полем "error" в теле ответа
            if (isset($json['error'])) {
                $isPhoneRejected = (int) $json['error'] === self::ERROR_INVALID_CLIENT_PHONE;

                Log::error(
                    $isPhoneRejected
                        ? 'Poster API rejected the client phone as non-existent'
                        : 'Poster API returned error in response body',
                    [
                        'order_id' => $order->id,
                        'error' => $json['error'],
                        'message' => $json['message'] ?? null,
                        'phone' => $phone,
                        'phone_region' => $isPhoneRejected ? PhoneNumber::regionCode($phone) : null,
                        'customer_phone' => $isPhoneRejected ? $order->customer_phone : null,
                        'body' => $response->body(),
                    ]
                );

                return null;
            }

            $data = $json['response'] ?? null;

            if (! is_array($data) || empty($data['incoming_order_id'])) {
                Log::error('Poster API returned unexpected response', [
                    'order_id' => $order->id,
                    'phone' => $phone,
                    'body' => $response->body(),
                ]);

                return null;
            }

            $order->update(['poster_order_id' => (string) $data['incoming_order_id']]);

            Log::info('Poster: incoming order created', [
                'order_id' => $order->id,
                'poster_order_id' => $data['incoming_order_id'],
            ]);

            return $data;
        } catch (\Exception $e) {
            Log::error('Poster API exception', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    protected function isPosterDelivery(Order $order): bool
    {
        return $order->delivery_type === DeliveryType::Delivery
            && filled($order->delivery_address);
    }

    protected function posterServiceMode(Order $order): int
    {
        return $this->isPosterDelivery($order) ? 3 : 2;
    }

    protected function posterDeliveryPrice(Order $order): ?int
    {
        if (! $this->isPosterDelivery($order)) {
            return null;
        }

        if ((float) $order->subtotal >= (float) config('delivery.free_from', 50)) {
            return null;
        }

        return (int) round((float) config('delivery.fee', 5) * 100);
    }

    protected function posterDeliveryTime(Order $order): ?int
    {
        if (blank($order->delivery_time)) {
            return null;
        }

        $deliveryAt = now()->setTimeFromTimeString($order->delivery_time);

        if ($deliveryAt->lte(now())) {
            $deliveryAt = $deliveryAt->addDay();
        }

        return $deliveryAt->getTimestamp();
    }

    protected function buildOrderComment(Order $order): string
    {
        $parts = $this->buildPricingCommentLines($order);
        $customerParts = [];

        if (filled($order->delivery_time)) {
            $customerParts[] = 'Ко времени: '.$order->delivery_time;
        }

        if (filled($order->promo_code)) {
            $customerParts[] = 'Промокод: '.$order->promo_code;
        }

        if (filled($order->comment)) {
            $customerParts[] = 'Комментарий клиента: '.$order->comment;
        }

        if ($customerParts !== []) {
            $parts[] = '---';
            $parts = array_merge($parts, $customerParts);
        }

        return implode("\n", $parts);
    }

    /**
     * @return list<string>
     */
    protected function buildPricingCommentLines(Order $order): array
    {
        $deliveryType = $order->delivery_type ?? DeliveryType::Delivery;
        $pricing = $this->discountService->calculateTotal((float) $order->subtotal, $deliveryType);
        $deliveryFee = (float) $order->delivery_fee;

        $lines = [
            'Сумма товаров: '.$this->formatMoney((float) $order->subtotal).' ₾',
        ];

        if ($pricing['discount_amount'] > 0 && $pricing['discount'] instanceof Discount) {
            $lines[] = 'Скидка: −'.$this->formatMoney($pricing['discount_amount']).' ₾ ('.$this->formatDiscountScopeLabel($pricing['discount']).')';
        }

        if ($deliveryType === DeliveryType::Pickup) {
            $lines[] = 'Доставка: —';
        } elseif ($deliveryFee > 0) {
            $lines[] = 'Доставка: '.$this->formatMoney($deliveryFee).' ₾';
        } else {
            $lines[] = 'Доставка: Бесплатно';
        }

        $lines[] = 'Итого к оплате: '.$this->formatMoney((float) $order->total).' ₾';
        $lines[] = 'Способ: '.($deliveryType === DeliveryType::Pickup ? 'самовывоз' : 'доставка');

        return $lines;
    }

    protected function formatDiscountScopeLabel(Discount $discount): string
    {
        $label = match ($discount->type) {
            DiscountType::Percent => '−'.(float) $discount->size.'%',
            DiscountType::Amount => '−'.$this->formatMoney((float) $discount->size).' ₾',
        };

        return match ($discount->scope) {
            DiscountScope::Pickup => 'самовывоз '.$label,
            DiscountScope::CartTotal => 'доставка '.$label.' от '.$this->formatMoney((float) $discount->min_cart_total, 0).' ₾',
        };
    }

    protected function formatMoney(float $amount, int $decimals = 2): string
    {
        return number_format($amount, $decimals, '.', '');
    }

    /**
     * @return array{product_id: int, count: int, modification?: string}
     */
    protected function buildDishProduct(OrderItem $item): array
    {
        $product = [
            'product_id' => $item->dish->poster_product_id,
            'count' => $item->quantity,
        ];

        $modification = $this->buildDishAddonModifications($item);

        if ($modification->isNotEmpty()) {
            $product['modification'] = $modification->toJson();
        }

        return $product;
    }

    /**
     * @return array{product_id: int, count: int}|null
     */
    protected function buildDrinkProduct(OrderItem $item): ?array
    {
        if (! $item->drink?->poster_product_id) {
            return null;
        }

        return [
            'product_id' => (int) $item->drink->poster_product_id,
            'count' => $item->quantity,
        ];
    }

    /**
     * @return Collection<int, array{m: int, a: int}>
     */
    protected function buildDishAddonModifications(OrderItem $item): Collection
    {
        $selectedAddons = collect($item->dish_addons ?? [])
            ->filter(fn ($addon) => is_array($addon) && ! empty($addon['id']));

        if ($selectedAddons->isEmpty() || ! $item->dish) {
            return collect();
        }

        $item->dish->loadMissing('addons');

        $modificationIds = $item->dish->addons
            ->filter(fn ($addon) => $addon->pivot?->poster_modification_id)
            ->mapWithKeys(fn ($addon) => [$addon->id => (int) $addon->pivot->poster_modification_id]);

        return $selectedAddons
            ->filter(fn ($addon) => $modificationIds->has($addon['id']))
            ->map(fn ($addon) => [
                'm' => (int) $modificationIds->get($addon['id']),
                'a' => max(1, (int) ($addon['quantity'] ?? 1)),
            ])
            ->sortBy('m')
            ->values();
    }

    /**
     * @return array{product_id: int, count: int, modification: string}|null
     */
    protected function buildBowlProduct(OrderItem $item): ?array
    {
        $constructorProductId = match ($item->item_type) {
            'breakfast' => (int) config('poster.breakfast_constructor_product_id'),
            default => (int) config('poster.constructor_product_id'),
        };

        if ($constructorProductId < 1) {
            $configKey = $item->item_type === 'breakfast'
                ? 'POSTER_BREAKFAST_CONSTRUCTOR_PRODUCT_ID'
                : 'POSTER_CONSTRUCTOR_PRODUCT_ID';

            Log::warning('Poster: constructor product id is not configured, item skipped', [
                'order_id' => $item->order_id,
                'order_item_id' => $item->id,
                'item_type' => $item->item_type,
                'config_key' => $configKey,
                'configured_value' => $constructorProductId,
            ]);

            return null;
        }

        $bowlProducts = collect($item->bowl_products ?? [])
            ->filter(fn ($product) => is_array($product) && ! empty($product['id']));

        $variantType = $item->item_type === 'breakfast' ? 'breakfast' : 'bowl';

        $modificationIds = ConstructorProduct::query()
            ->whereIn('id', $bowlProducts->pluck('id'))
            ->with(['variants' => fn ($query) => $query->where('type', $variantType)->whereNotNull('poster_modification_id')])
            ->get()
            ->mapWithKeys(function (ConstructorProduct $product) use ($variantType) {
                $variant = $product->variantFor($variantType);

                return $variant?->poster_modification_id
                    ? [$product->id => (int) $variant->poster_modification_id]
                    : [];
            });

        $modification = $bowlProducts
            ->filter(fn ($product) => $modificationIds->has($product['id']))
            ->map(fn ($product) => [
                'm' => (int) $modificationIds->get($product['id']),
                'a' => max(1, (int) ($product['quantity'] ?? 1)),
            ])
            ->sortBy('m')
            ->values();

        if ($modification->isEmpty()) {
            Log::warning('Poster: constructor item has no products mapped to Poster modifications, item skipped', [
                'order_id' => $item->order_id,
                'order_item_id' => $item->id,
                'item_type' => $item->item_type,
                'bowl_products' => $item->bowl_products,
            ]);

            return null;
        }

        return [
            'product_id' => $constructorProductId,
            'count' => $item->quantity,
            'modification' => $modification->toJson(),
        ];
    }
}
