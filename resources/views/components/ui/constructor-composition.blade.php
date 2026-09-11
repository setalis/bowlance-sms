@props([
    'item',
])

<div class="mt-2">
    <p class="mb-1 text-xs text-base-content/60">{{ $item->item_type === 'breakfast' ? 'Состав завтрака:' : 'Состав боула:' }}</p>
    <ul class="space-y-1">
        @foreach($item->bowl_products as $product)
            @php
                $quantity = $item->constructorProductQuantity($product);
            @endphp
            <li class="flex items-center justify-between gap-2 text-xs">
                <span class="text-base-content/70">
                    {{ $product['name'] }}
                    @if($quantity > 1)
                        ×{{ $quantity }}
                    @endif
                </span>
                <span class="tabular-nums text-base-content/40">{{ number_format($item->constructorProductLinePrice($product), 2) }} ₾</span>
            </li>
        @endforeach
    </ul>
</div>
