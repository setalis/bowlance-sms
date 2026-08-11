@props([
    'context' => 'checkout',
])

<div {{ $attributes->merge(['class' => 'rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100/60 dark:from-emerald-950/30 dark:to-emerald-900/10 border border-emerald-200/60 dark:border-emerald-800/40 p-4 space-y-3']) }}>
    <div class="flex items-center justify-between gap-3">
        <span class="text-sm text-base-content/70">{{ __('frontend.cart_subtotal') }}</span>
        <span class="text-sm font-semibold tabular-nums"
              x-text="(typeof subtotal !== 'undefined' ? subtotal : $store.cart.subtotal).toFixed(2) + ' ₾'"></span>
    </div>

    <div class="flex items-start gap-2 text-xs text-sky-700/80 dark:text-sky-300/80">
        <span class="icon-[tabler--truck-delivery] size-4 shrink-0 mt-0.5"></span>
        <span x-text="$store.cart.deliveryFeeHint?.label"></span>
    </div>

    <div x-show="!$store.cart.deliveryFeeHint?.isFree && $store.cart.deliveryFeeHint?.addMoreLabel"
         x-cloak
         class="text-xs text-amber-700/80 dark:text-amber-300/80 pl-6"
         x-text="$store.cart.deliveryFeeHint?.addMoreLabel">
    </div>

    <div x-show="$store.cart.pickupHint"
         x-cloak
         class="flex items-start gap-2 text-xs text-emerald-700/80 dark:text-emerald-300/80">
        <span class="icon-[tabler--walk] size-4 shrink-0 mt-0.5"></span>
        <span x-text="$store.cart.pickupHint?.label"></span>
    </div>

    <div x-show="$store.cart.deliveryHint"
         x-cloak
         class="flex items-start gap-2 text-xs text-emerald-700/80 dark:text-emerald-300/80">
        <span class="icon-[tabler--discount-2] size-4 shrink-0 mt-0.5"></span>
        <span x-text="$store.cart.deliveryHint?.label"></span>
    </div>

    @if($context === 'checkout')
        <div x-show="typeof showFinalTotal !== 'undefined' && showFinalTotal && discountAmount > 0"
             x-cloak
             class="flex items-center justify-between gap-3 text-sm">
            <span class="text-emerald-700 dark:text-emerald-300">{{ __('frontend.discount_line') }}</span>
            <span class="font-semibold text-emerald-700 dark:text-emerald-300 tabular-nums"
                  x-text="'−' + discountAmount.toFixed(2) + ' ₾'"></span>
        </div>

        <div x-show="typeof showFinalTotal !== 'undefined' && showFinalTotal && formData.deliveryType === 'delivery'"
             x-cloak
             class="flex items-center justify-between gap-3 text-sm">
            <span class="text-base-content/70">{{ __('frontend.delivery_fee_line') }}</span>
            <span class="font-semibold tabular-nums"
                  :class="deliveryFee > 0 ? 'text-base-content' : 'text-emerald-700 dark:text-emerald-300'"
                  x-text="deliveryFee > 0 ? deliveryFee.toFixed(2) + ' ₾' : '{{ __('frontend.delivery_fee_free') }}'"></span>
        </div>

        <div class="flex items-center justify-between gap-3 pt-1 border-t border-emerald-200/60 dark:border-emerald-800/40">
            <div>
                <p class="text-xs font-medium text-emerald-700 dark:text-emerald-300 mb-0.5">{{ __('frontend.total_to_pay') }}</p>
                <p x-show="typeof showFinalTotal !== 'undefined' && !showFinalTotal"
                   x-cloak
                   class="text-xs text-emerald-600/70 dark:text-emerald-400/70">
                    {{ __('frontend.choose_delivery_on_next_step') }}
                </p>
                <p x-show="typeof showFinalTotal !== 'undefined' && showFinalTotal && appliedDiscountMessage"
                   x-cloak
                   x-text="appliedDiscountMessage"
                   class="text-xs text-emerald-600/70 dark:text-emerald-400/70">
                </p>
            </div>
            <p class="text-2xl font-black text-emerald-700 dark:text-emerald-300 tabular-nums"
               x-text="(typeof totalToPay !== 'undefined' ? totalToPay : $store.cart.subtotal).toFixed(2) + ' ₾'"></p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-2 pt-1 border-t border-emerald-200/60 dark:border-emerald-800/40">
            <div class="flex items-center justify-between gap-3 text-sm">
                <span class="text-base-content/70">{{ __('frontend.total_pickup_preview') }}</span>
                <span class="font-bold text-emerald-700 dark:text-emerald-300 tabular-nums"
                      x-text="$store.cart.pickupTotalPreview.toFixed(2) + ' ₾'"></span>
            </div>
            <div class="flex items-center justify-between gap-3 text-sm">
                <span class="text-base-content/70">{{ __('frontend.total_delivery_preview') }}</span>
                <span class="font-bold text-emerald-700 dark:text-emerald-300 tabular-nums"
                      x-text="$store.cart.deliveryTotalPreview.toFixed(2) + ' ₾'"></span>
            </div>
        </div>
    @endif
</div>
