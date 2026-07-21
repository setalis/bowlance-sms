<?php

namespace App\Services;

use App\Models\ConstructorProduct;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PosterService
{
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

        $order->loadMissing('items.dish');

        $products = $order->items
            ->filter(fn ($item) => $item->item_type === 'dish' && $item->dish?->poster_product_id)
            ->map(fn ($item) => [
                'product_id' => $item->dish->poster_product_id,
                'count' => $item->quantity,
            ])
            ->values()
            ->all();

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
                    'poster_product_id' => $item->dish?->poster_product_id,
                    'bowl_product_ids' => collect($item->bowl_products ?? [])->pluck('id')->all(),
                ])->all(),
            ]);

            return null;
        }

        $payload = [
            'spot_id' => config('poster.spot_id'),
            'phone' => $order->customer_phone,
            'first_name' => $order->customer_name,
            'address' => $order->delivery_address,
            'comment' => $order->comment,
            'service_mode' => $order->delivery_type?->value === 'pickup' ? 2 : 3,
            'products' => $products,
        ];

        Log::info('Poster: sending incoming order', [
            'order_id' => $order->id,
            'products' => $products,
        ]);

        try {
            $response = Http::post(
                'https://joinposter.com/api/incomingOrders.createIncomingOrder?token='.config('poster.token'),
                $payload
            );

            if ($response->failed()) {
                Log::error('Poster API error', [
                    'order_id' => $order->id,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $json = $response->json();

            // Poster возвращает ошибки с HTTP 200 и полем "error" в теле ответа
            if (isset($json['error'])) {
                Log::error('Poster API returned error in response body', [
                    'order_id' => $order->id,
                    'error' => $json['error'],
                    'message' => $json['message'] ?? null,
                    'body' => $response->body(),
                ]);

                return null;
            }

            $data = $json['response'] ?? null;

            if (! is_array($data) || empty($data['incoming_order_id'])) {
                Log::error('Poster API returned unexpected response', [
                    'order_id' => $order->id,
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

        $modificationColumn = $item->item_type === 'breakfast'
            ? 'poster_breakfast_modification_id'
            : 'poster_bowl_modification_id';

        $modificationIds = ConstructorProduct::query()
            ->whereIn('id', $bowlProducts->pluck('id'))
            ->whereNotNull($modificationColumn)
            ->pluck($modificationColumn, 'id');

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
