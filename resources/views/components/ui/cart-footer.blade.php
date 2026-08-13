@props([
    'context' => 'cart',
])

<div {{ $attributes->merge(['class' => 'border-t border-base-200 bg-base-100']) }}
     x-data="{ detailsOpen: false }"
     x-effect="void $store.cart.pricingVersion">
    <div class="px-4 pt-3 pb-3 pb-safe">
        <button type="button"
                x-show="typeof hasSummaryDetails !== 'undefined' ? hasSummaryDetails : $store.cart.hasSummaryDetails"
                x-cloak
                @click="detailsOpen = !detailsOpen"
                class="flex items-center gap-1 text-xs text-base-content/50 hover:text-base-content/70 transition-colors mb-2">
            <span x-text="detailsOpen ? '{{ __('frontend.order_details_hide') }}' : '{{ __('frontend.order_details') }}'"></span>
            <span class="icon-[tabler--chevron-down] size-3.5 transition-transform duration-200"
                  :class="detailsOpen && 'rotate-180'"></span>
        </button>

        <div x-show="detailsOpen"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-cloak
             class="mb-3 rounded-xl bg-base-200/40 px-3 py-2">
            <x-ui.order-breakdown :context="$context" />
        </div>

        <div class="grid grid-cols-3 gap-2 mb-3">
            <div class="min-w-0 text-center">
                <p class="text-[10px] text-base-content/50 leading-tight truncate">{{ __('frontend.total_to_pay') }}</p>
                <p class="text-sm font-bold tabular-nums text-base-content leading-tight mt-0.5"
                   x-text="((typeof footerTotal !== 'undefined' ? footerTotal : $store.cart.footerTotal).toFixed(2)) + ' ₾'"></p>
            </div>
            <div class="min-w-0 text-center">
                <p class="text-[10px] text-base-content/50 leading-tight truncate">{{ __('frontend.delivery_fee_line') }}</p>
                <p class="text-sm font-bold tabular-nums leading-tight mt-0.5"
                   :class="((typeof footerDeliveryFee !== 'undefined' ? footerDeliveryFee : $store.cart.footerDeliveryFee) > 0) ? 'text-base-content' : 'text-emerald-600'"
                   x-text="((typeof footerDeliveryFee !== 'undefined' ? footerDeliveryFee : $store.cart.footerDeliveryFee) > 0)
                       ? ((typeof footerDeliveryFee !== 'undefined' ? footerDeliveryFee : $store.cart.footerDeliveryFee).toFixed(2) + ' ₾')
                       : '{{ __('frontend.delivery_fee_free') }}'"></p>
            </div>
            <div class="min-w-0 text-center">
                <p class="text-[10px] text-base-content/50 leading-tight truncate">{{ __('frontend.discount_line') }}</p>
                <p class="text-sm font-bold tabular-nums leading-tight mt-0.5"
                   :class="((typeof footerDiscountAmount !== 'undefined' ? footerDiscountAmount : $store.cart.footerDiscountAmount) > 0) ? 'text-emerald-600' : 'text-base-content/40'"
                   x-text="((typeof footerDiscountAmount !== 'undefined' ? footerDiscountAmount : $store.cart.footerDiscountAmount) > 0)
                       ? ('−' + (typeof footerDiscountAmount !== 'undefined' ? footerDiscountAmount : $store.cart.footerDiscountAmount).toFixed(2) + ' ₾')
                       : '{{ __('frontend.summary_no_discount') }}'"></p>
            </div>
        </div>

        @if($context === 'cart')
            <div class="w-full">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>
