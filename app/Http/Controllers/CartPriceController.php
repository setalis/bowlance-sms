<?php

namespace App\Http\Controllers;

use App\Enums\OrderMode;
use App\Services\OrderItemPriceResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartPriceController extends Controller
{
    public function __invoke(Request $request, OrderItemPriceResolver $resolver): JsonResponse
    {
        $mode = OrderMode::current();

        if ($mode === null) {
            return response()->json([
                'message' => 'Сначала выберите способ заказа.',
            ], 409);
        }

        $payload = $request->validate([
            'items' => ['present', 'array'],
            'items.*.type' => ['required', 'in:dish,bowl,drink,breakfast'],
            'items.*.id' => ['nullable'],
            'items.*.name' => ['nullable', 'string'],
            'items.*.price' => ['nullable', 'numeric'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1'],
            'items.*.products' => ['nullable', 'array'],
            'items.*.addons' => ['nullable', 'array'],
        ]);

        $items = $resolver->priceItems($payload['items'], $mode);
        $total = round(collect($items)->sum(
            fn (array $item): float => (float) $item['price'] * (int) ($item['quantity'] ?? 1)
        ), 2);

        return response()->json([
            'items' => $items,
            'total' => $total,
        ]);
    }
}
