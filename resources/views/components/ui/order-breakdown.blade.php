@props([
    'context' => 'cart',
])

<div {{ $attributes->merge(['class' => 'space-y-2 text-sm']) }}>
    <div class="flex items-center justify-between gap-3 py-1">
        <span class="text-base-content/70">{{ __('frontend.cart_subtotal') }}</span>
        <span class="font-medium tabular-nums"
              x-text="(typeof subtotal !== 'undefined' ? subtotal : $store.cart.subtotal).toFixed(2) + ' ₾'"></span>
    </div>

    <template x-if="typeof showFinalTotal !== 'undefined' && showFinalTotal && formData.deliveryType === 'delivery'">
        <div class="flex items-center justify-between gap-3 py-1">
            <span class="text-base-content/70">{{ __('frontend.delivery_fee_line') }}</span>
            <span class="font-medium tabular-nums"
                  :class="deliveryFee > 0 ? 'text-base-content' : 'text-emerald-600'"
                  x-text="deliveryFee > 0 ? deliveryFee.toFixed(2) + ' ₾' : '{{ __('frontend.delivery_fee_free') }}'"></span>
        </div>
    </template>

    <template x-if="typeof showFinalTotal !== 'undefined' && showFinalTotal && discountAmount > 0">
        <div class="flex items-center justify-between gap-3 py-1">
            <span class="text-emerald-600">{{ __('frontend.discount_line') }}</span>
            <span class="font-medium tabular-nums text-emerald-600"
                  x-text="'−' + discountAmount.toFixed(2) + ' ₾'"></span>
        </div>
    </template>

    @if($context === 'cart')
        <div class="flex items-center justify-between gap-3 py-1">
            <span class="text-base-content/70">{{ __('frontend.total_pickup_preview') }}</span>
            <span class="font-medium tabular-nums text-base-content"
                  x-text="$store.cart.pickupTotalPreview.toFixed(2) + ' ₾'"></span>
        </div>
        <div class="flex items-center justify-between gap-3 py-1">
            <span class="text-base-content/70">{{ __('frontend.total_delivery_preview') }}</span>
            <span class="font-medium tabular-nums text-base-content"
                  x-text="$store.cart.deliveryTotalPreview.toFixed(2) + ' ₾'"></span>
        </div>
    @else
        <template x-if="typeof showFinalTotal !== 'undefined' && !showFinalTotal">
            <div class="flex items-center justify-between gap-3 py-1">
                <span class="text-base-content/70">{{ __('frontend.total_pickup_preview') }}</span>
                <span class="font-medium tabular-nums"
                      x-text="$store.cart.pickupTotalPreview.toFixed(2) + ' ₾'"></span>
            </div>
        </template>
        <template x-if="typeof showFinalTotal !== 'undefined' && !showFinalTotal">
            <div class="flex items-center justify-between gap-3 py-1">
                <span class="text-base-content/70">{{ __('frontend.total_delivery_preview') }}</span>
                <span class="font-medium tabular-nums"
                      x-text="$store.cart.deliveryTotalPreview.toFixed(2) + ' ₾'"></span>
            </div>
        </template>
    @endif

    <div class="flex items-center justify-between gap-3 pt-2 border-t border-base-200 font-semibold">
        <span>{{ __('frontend.total_to_pay') }}</span>
        <span class="tabular-nums text-emerald-600"
              x-text="(typeof footerTotal !== 'undefined' ? footerTotal : $store.cart.footerTotal).toFixed(2) + ' ₾'"></span>
    </div>

    <template x-if="$store.cart.pickupHint || $store.cart.deliveryFeeHint || $store.cart.deliveryHint">
        <div class="pt-2 mt-1 border-t border-base-200/60 space-y-1.5">
            <p class="text-[10px] font-semibold uppercase tracking-wider text-base-content/40">{{ __('frontend.promotions_section') }}</p>
            <template x-if="$store.cart.pickupHint">
                <p class="text-xs text-base-content/60 leading-snug" x-text="$store.cart.pickupHint.label"></p>
            </template>
            <template x-if="$store.cart.deliveryFeeHint">
                <p class="text-xs text-base-content/60 leading-snug" x-text="$store.cart.deliveryFeeHint.label"></p>
            </template>
            <template x-if="$store.cart.deliveryHint">
                <p class="text-xs text-base-content/60 leading-snug" x-text="$store.cart.deliveryHint.label"></p>
            </template>
        </div>
    </template>
</div>
