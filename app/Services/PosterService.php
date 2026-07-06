<?php

namespace App\Services;

use App\Models\Order;
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

        if (empty($products)) {
            Log::warning('Poster: no order items mapped to Poster products, order skipped', [
                'order_id' => $order->id,
                'items' => $order->items->map(fn ($item) => [
                    'item_type' => $item->item_type,
                    'dish_id' => $item->dish_id,
                    'poster_product_id' => $item->dish?->poster_product_id,
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
            'products' => $products,
        ];

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
}
