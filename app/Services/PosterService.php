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

            $data = $response->json('response');

            if (! empty($data['incoming_order_id'])) {
                $order->update(['poster_order_id' => (string) $data['incoming_order_id']]);
            }

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
