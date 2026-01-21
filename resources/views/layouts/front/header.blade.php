<header class="navbar fixed top-0 z-50">
    <div class="container mx-auto flex items-center justify-between px-4">
        <!-- Логотип -->
        <div class="navbar-start">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-2xl font-bold text-primary">
                <span class="icon-[tabler--bowl] size-8"></span>
                <span>Bowlance</span>
            </a>
        </div>

        <!-- Центральная часть - иконки -->
        <div class="navbar-center hidden gap-6 lg:flex">
            <a href="tel:+995555123456" class="btn btn-ghost btn-sm gap-2" aria-label="{{ __('frontend.phone') }}">
                <span class="icon-[tabler--phone] size-5"></span>
                <span>+995 555 123 456</span>
            </a>
            
            <a href="https://instagram.com/bowlance" target="_blank" class="btn btn-ghost btn-sm gap-2" aria-label="Instagram">
                <span class="icon-[tabler--brand-instagram] size-5"></span>
            </a>
            
            <button type="button" class="btn btn-ghost btn-sm gap-2" aria-label="{{ __('frontend.location') }}">
                <span class="icon-[tabler--map-pin] size-5"></span>
                <span>{{ __('frontend.location') }}</span>
            </button>
        </div>

        <!-- Правая часть - язык и корзина -->        


        <div class="navbar-end flex items-center gap-2">
            <div class="dropdown relative inline-flex [--placement:bottom-end]">
              <button id="locale-dropdown" 
                      type="button" 
                      class="dropdown-toggle btn btn-primary btn-sm gap-2" 
                      aria-haspopup="menu"
                      aria-expanded="false" 
                      aria-label="Выбрать язык">
                <span class="icon-[tabler--language] size-4"></span>
                <span>{{ strtoupper(app()->getLocale()) }}</span>
                <span class="icon-[tabler--chevron-down] dropdown-open:rotate-180 size-4 transition-transform"></span>
              </button>
              <ul class="dropdown-menu dropdown-open:opacity-100 hidden w-40 space-y-0.5 bg-base-100 rounded-box shadow-lg border border-base-content/10 py-2" 
                  role="menu"
                  aria-orientation="vertical" 
                  aria-labelledby="locale-dropdown">
                <li>
                    <a href="{{ route('locale.switch', 'ru') }}" 
                       class="dropdown-item px-4 py-2.5 flex items-center gap-2 {{ app()->getLocale() === 'ru' ? 'dropdown-active' : '' }}">
                        <span class="icon-[tabler--flag] size-4"></span>
                        <span>Русский</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('locale.switch', 'ka') }}" 
                       class="dropdown-item px-4 py-2.5 flex items-center gap-2 {{ app()->getLocale() === 'ka' ? 'dropdown-active' : '' }}">
                        <span class="icon-[tabler--flag] size-4"></span>
                        <span>ქართული</span>
                    </a>
                </li>
              </ul>
            </div>
            

            <!-- Корзина -->
            <button type="button" 
                    class="btn btn-primary btn-sm gap-2 relative" 
                    aria-label="{{ __('frontend.cart') }}" 
                    @click="$store.cart.openDrawer()"
                    x-data>
                <span class="icon-[tabler--shopping-cart] size-5"></span>
                <span class="badge badge-secondary badge-sm absolute -top-1 -right-1" 
                      x-show="$store.cart.totalItems > 0"
                      x-text="$store.cart.totalItems"
                      x-cloak></span>
            </button>

            <!-- Меню для мобильных -->
            <button type="button" class="btn btn-ghost btn-square btn-sm lg:hidden" data-hs-overlay="#mobileMenu" aria-label="{{ __('frontend.menu') }}">
                <span class="icon-[tabler--menu-2] size-6"></span>
            </button>
        </div>
    </div>
</header>

<!-- Отступ для fixed header -->
<div class="h-16"></div>

<!-- Мобильное меню -->
<div id="mobileMenu" class="hs-overlay hs-overlay-open:translate-x-0 hidden translate-x-full fixed top-0 end-0 transition-all duration-300 transform h-full max-w-xs w-full z-[80] bg-base-100 border-s" role="dialog" tabindex="-1">
    <div class="flex flex-col h-full">
        <div class="flex justify-between items-center py-3 px-4 border-b">
            <h3 class="font-bold text-lg">{{ __('frontend.menu') }}</h3>
            <button type="button" class="btn btn-text btn-circle btn-sm" aria-label="{{ __('frontend.close') }}" data-hs-overlay="#mobileMenu">
                <span class="icon-[tabler--x] size-5"></span>
            </button>
        </div>
        <div class="p-4 overflow-y-auto">
            <div class="flex flex-col gap-4">
                <a href="tel:+995555123456" class="btn btn-outline gap-2">
                    <span class="icon-[tabler--phone] size-5"></span>
                    <span>+995 555 123 456</span>
                </a>
                
                <a href="https://instagram.com/bowlance" target="_blank" class="btn btn-outline gap-2">
                    <span class="icon-[tabler--brand-instagram] size-5"></span>
                    <span>Instagram</span>
                </a>
                
                <button type="button" class="btn btn-outline gap-2">
                    <span class="icon-[tabler--map-pin] size-5"></span>
                    <span>{{ __('frontend.location') }}</span>
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Offcanvas Корзина -->
<div x-data x-on:keydown.esc.prevent="$store.cart.isOpen = false">
    <!-- Offcanvas Backdrop -->
    <div
        x-cloak
        x-show="$store.cart.isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-bind:aria-hidden="!$store.cart.isOpen"
        tabindex="-1"
        role="dialog"
        class="z-90 fixed inset-0 overflow-hidden bg-zinc-700/75 backdrop-blur-xs dark:bg-zinc-950/50"
    >
        <!-- Offcanvas Sidebar -->
        <div
            x-cloak
            x-show="$store.cart.isOpen"
            x-on:click.away="$store.cart.isOpen = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full rtl:-translate-x-full"
            x-transition:enter-end="translate-x-0 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0 translate-y-0"
            x-transition:leave-end="translate-x-full rtl:-translate-x-full"
            role="document"
            class="absolute flex flex-col bg-base-100 shadow-lg will-change-transform h-dvh top-0 end-0 w-full sm:max-w-md"
        >
            <!-- Header -->
            <div class="flex min-h-16 flex-none items-center justify-between border-b border-base-content/10 px-5">
                <div class="py-5">
                    <h3 class="font-bold text-lg">{{ __('frontend.cart_title') }}</h3>
                    <p class="text-sm text-base-content/60" x-show="$store.cart.totalItems > 0" x-cloak>
                        <span x-text="$store.cart.totalItems"></span> 
                        <span x-text="$store.cart.totalItems === 1 ? '{{ __('frontend.items_single') }}' : ($store.cart.totalItems < 5 ? '{{ __('frontend.items_few') }}' : '{{ __('frontend.items_many') }}')"></span>
                    </p>
                </div>
                <button
                    @click="$store.cart.isOpen = false"
                    type="button"
                    class="btn btn-text btn-circle btn-sm"
                >
                    <span class="icon-[tabler--x] size-5"></span>
                </button>
            </div>
            <!-- END Header -->

            <!-- Content -->
            <div class="flex grow flex-col overflow-y-auto p-5">
            <!-- Пустая корзина -->
            <div x-show="$store.cart.items.length === 0" 
                 x-cloak
                 class="flex flex-col items-center justify-center py-12 text-center">
                <span class="icon-[tabler--shopping-cart-off] mb-4 size-16 text-base-content/30"></span>
                <p class="text-base-content/60 mb-2 text-lg">{{ __('frontend.cart_empty') }}</p>
                <p class="text-base-content/40 text-sm">{{ __('frontend.cart_empty_desc') }}</p>
            </div>

            <!-- Список товаров -->
            <div x-show="$store.cart.items.length > 0" x-cloak class="space-y-4">
                <template x-for="(item, index) in $store.cart.items" :key="index">
                    <div class="card bg-base-200/50">
                        <div class="card-body p-4">
                            <div class="flex gap-3">
                                <!-- Изображение -->
                                <div class="shrink-0">
                                    <template x-if="item.type === 'dish' && item.image">
                                        <img :src="'/storage/' + item.image" 
                                             :alt="item.name" 
                                             class="size-20 rounded-lg object-cover">
                                    </template>
                                    <template x-if="item.type === 'dish' && !item.image">
                                        <div class="size-20 rounded-lg bg-base-300 flex items-center justify-center">
                                            <span class="icon-[tabler--bowl] size-8 text-base-content/30"></span>
                                        </div>
                                    </template>
                                    <template x-if="item.type === 'bowl'">
                                        <div class="size-20 rounded-lg bg-primary/20 flex items-center justify-center">
                                            <span class="icon-[tabler--tools-kitchen-2] size-8 text-primary"></span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Информация -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-medium truncate" x-text="item.name"></h4>
                                            <template x-if="item.type === 'bowl' && item.products">
                                                <p class="text-xs text-base-content/60 mt-1">
                                                    <span x-text="item.products.length"></span> {{ __('frontend.ingredients') }}
                                                </p>
                                            </template>
                                            <template x-if="item.weight">
                                                <p class="text-xs text-base-content/50" x-text="item.weight"></p>
                                            </template>
                                        </div>
                                        <button type="button" 
                                                @click="$store.cart.removeItem(index)"
                                                class="btn btn-ghost btn-circle btn-xs"
                                                aria-label="{{ __('frontend.remove') }}">
                                            <span class="icon-[tabler--trash] size-4"></span>
                                        </button>
                                    </div>

                                    <!-- Пищевая ценность (если есть) -->
                                    <template x-if="item.calories > 0">
                                        <div class="flex flex-wrap gap-1 mb-2 text-xs">
                                            <span class="badge badge-outline badge-xs">
                                                <span class="icon-[tabler--flame] mr-0.5 size-3"></span>
                                                <span x-text="Math.round(item.calories)"></span> {{ __('frontend.calories') }}
                                            </span>
                                            <template x-if="item.proteins > 0">
                                                <span class="badge badge-outline badge-xs">
                                                    {{ __('frontend.proteins') }}: <span x-text="item.proteins.toFixed(1)"></span>{{ __('frontend.grams') }}
                                                </span>
                                            </template>
                                            <template x-if="item.fats > 0">
                                                <span class="badge badge-outline badge-xs">
                                                    {{ __('frontend.fats') }}: <span x-text="item.fats.toFixed(1)"></span>{{ __('frontend.grams') }}
                                                </span>
                                            </template>
                                            <template x-if="item.carbs > 0">
                                                <span class="badge badge-outline badge-xs">
                                                    {{ __('frontend.carbs') }}: <span x-text="item.carbs.toFixed(1)"></span>{{ __('frontend.grams') }}
                                                </span>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Количество и цена -->
                                    <div class="flex items-center justify-between">
                                        <div class="join">
                                            <button type="button" 
                                                    @click="$store.cart.decreaseQuantity(index)"
                                                    class="btn btn-xs join-item"
                                                    :disabled="item.quantity <= 1">
                                                <span class="icon-[tabler--minus] size-3"></span>
                                            </button>
                                            <div class="btn btn-xs join-item no-animation pointer-events-none">
                                                <span x-text="item.quantity"></span>
                                            </div>
                                            <button type="button" 
                                                    @click="$store.cart.increaseQuantity(index)"
                                                    class="btn btn-xs join-item">
                                                <span class="icon-[tabler--plus] size-3"></span>
                                            </button>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold" x-text="(item.price * item.quantity).toFixed(2) + ' ₾'"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Список продуктов в боуле (раскрывающийся) -->
                            <template x-if="item.type === 'bowl' && item.products && item.products.length > 0">
                                <details class="collapse collapse-arrow mt-2 border-t border-base-content/10 pt-2">
                                    <summary class="collapse-title min-h-0 p-0 text-xs font-medium cursor-pointer">
                                        {{ __('frontend.bowl_composition') }}
                                    </summary>
                                    <div class="collapse-content p-0 pt-2">
                                        <ul class="space-y-1">
                                            <template x-for="product in item.products" :key="product.id">
                                                <li class="flex items-center justify-between text-xs">
                                                    <span x-text="product.name" class="text-base-content/70"></span>
                                                    <span x-text="product.price.toFixed(2) + ' ₾'" class="text-base-content/50"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </details>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Кнопка очистить корзину -->
                <button type="button" 
                        @click="$store.cart.clearCart()"
                        class="btn btn-ghost btn-sm w-full gap-2">
                    <span class="icon-[tabler--trash] size-4"></span>
                    {{ __('frontend.clear_cart') }}
                </button>
            </div>
            </div>
            <!-- END Content -->

            <!-- Footer - всегда прижат к низу -->
            <div class="flex-none border-t border-base-content/10 bg-base-100" x-show="$store.cart.items.length > 0" x-cloak>
                <div class="p-4">
            <!-- Общая пищевая ценность -->
            <div class="mb-4 rounded-lg bg-base-200/50 p-3">
                <p class="text-xs font-medium mb-2 text-base-content/70">{{ __('frontend.nutrition') }}:</p>
                <div class="grid grid-cols-4 gap-2 text-center">
                    <div>
                        <p class="text-xs text-base-content/50">{{ __('frontend.nutrition_calories') }}</p>
                        <p class="text-sm font-bold" x-text="Math.round($store.cart.totalNutrition.calories)"></p>
                    </div>
                    <div>
                        <p class="text-xs text-base-content/50">{{ __('frontend.nutrition_proteins') }}</p>
                        <p class="text-sm font-bold" x-text="$store.cart.totalNutrition.proteins.toFixed(1) + '{{ __('frontend.grams') }}'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-base-content/50">{{ __('frontend.nutrition_fats') }}</p>
                        <p class="text-sm font-bold" x-text="$store.cart.totalNutrition.fats.toFixed(1) + '{{ __('frontend.grams') }}'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-base-content/50">{{ __('frontend.nutrition_carbs') }}</p>
                        <p class="text-sm font-bold" x-text="$store.cart.totalNutrition.carbs.toFixed(1) + '{{ __('frontend.grams') }}'"></p>
                    </div>
                </div>
            </div>

            <!-- Итоговая сумма -->
            <div class="mb-4 flex items-center justify-between">
                <span class="text-lg font-medium">{{ __('frontend.total_price') }}</span>
                <span class="text-2xl font-bold text-primary" x-text="$store.cart.totalPrice.toFixed(2) + ' ₾'"></span>
            </div>

            <!-- Кнопка оформления -->
            <button type="button" 
                    class="btn btn-primary w-full gap-2"
                    :disabled="$store.cart.items.length === 0"
                    @click="$dispatch('open-checkout-modal')">
                <span class="icon-[tabler--check] size-5"></span>
                {{ __('frontend.checkout') }}
            </button>
                </div>
            </div>
            <!-- END Footer -->
        </div>
        <!-- END Offcanvas Sidebar -->
    </div>
    <!-- END Offcanvas Backdrop -->
</div>
<!-- END Offcanvas Корзина -->

<!-- Модальное окно оформления заказа -->
<div x-data="checkoutModal()" 
     x-on:open-checkout-modal.window="open = true"
     x-on:keydown.esc.prevent="open = false">
    <!-- Backdrop -->
    <div
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] overflow-hidden bg-zinc-700/75 backdrop-blur-xs"
    >
        <!-- Modal -->
        <div
            x-cloak
            x-show="open"
            x-on:click.away="open = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="flex min-h-screen items-center justify-center p-4"
        >
            <div class="w-full max-w-md rounded-lg bg-base-100 p-6 shadow-xl">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-xl font-bold">{{ __('frontend.checkout_title') }}</h3>
                    <button @click="open = false" class="btn btn-circle btn-ghost btn-sm">
                        <span class="icon-[tabler--x] size-5"></span>
                    </button>
                </div>

                <form @submit.prevent="submitOrder">
                    <div class="space-y-4">
                        <!-- Имя -->
                        <div>
                            <label class="label">
                                <span class="label-text">{{ __('frontend.your_name') }} <span class="text-error">{{ __('frontend.required') }}</span></span>
                            </label>
                            <input type="text" 
                                   x-model="formData.name" 
                                   class="input input-bordered w-full" 
                                   required
                                   :placeholder="'{{ __('frontend.name_placeholder') }}'">
                        </div>

                        <!-- Телефон -->
                        <div>
                            <label class="label">
                                <span class="label-text">{{ __('frontend.phone') }} <span class="text-error">{{ __('frontend.required') }}</span></span>
                            </label>
                            <input type="tel" 
                                   x-model="formData.phone" 
                                   class="input input-bordered w-full" 
                                   required
                                   :placeholder="'{{ __('frontend.phone_placeholder') }}'">
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="label">
                                <span class="label-text">{{ __('frontend.email') }}</span>
                            </label>
                            <input type="email" 
                                   x-model="formData.email" 
                                   class="input input-bordered w-full" 
                                   :placeholder="'{{ __('frontend.email_placeholder') }}'">
                        </div>

                        <!-- Адрес доставки -->
                        <div>
                            <label class="label">
                                <span class="label-text">{{ __('frontend.delivery_address') }}</span>
                            </label>
                            <textarea x-model="formData.address" 
                                      class="textarea textarea-bordered w-full" 
                                      rows="2"
                                      :placeholder="'{{ __('frontend.address_placeholder') }}'"></textarea>
                        </div>

                        <!-- Комментарий -->
                        <div>
                            <label class="label">
                                <span class="label-text">{{ __('frontend.order_comment') }}</span>
                            </label>
                            <textarea x-model="formData.comment" 
                                      class="textarea textarea-bordered w-full" 
                                      rows="2"
                                      :placeholder="'{{ __('frontend.comment_placeholder') }}'"></textarea>
                        </div>

                        <!-- Итоговая сумма -->
                        <div class="rounded-lg bg-base-200 p-4">
                            <div class="flex items-center justify-between text-lg font-bold">
                                <span>{{ __('frontend.total_to_pay') }}</span>
                                <span class="text-primary" x-text="$store.cart.totalPrice.toFixed(2) + ' ₾'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопки -->
                    <div class="mt-6 flex gap-3">
                        <button type="button" 
                                @click="open = false" 
                                class="btn btn-ghost flex-1">
                            {{ __('frontend.cancel') }}
                        </button>
                        <button type="submit" 
                                class="btn btn-primary flex-1 gap-2"
                                :disabled="loading">
                            <span x-show="!loading" class="icon-[tabler--check] size-5"></span>
                            <span x-show="loading" class="loading loading-spinner loading-sm"></span>
                            <span x-text="loading ? '{{ __('frontend.submitting') }}' : '{{ __('frontend.submit') }}'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END Модальное окно -->
