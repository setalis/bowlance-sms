<header class="navbar h-20 fixed z-50 border-b border-base-content/10">
    <div class="container mx-auto flex items-center justify-between gap-6 md:gap-6">
        <!-- Логотип -->
        <div class="navbar-start">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-2xl font-bold text-primary">
                <!-- <span class="icon-[tabler--bowl] size-8"></span>
                <span>Bowlance</span> -->
                <img src="{{ asset('storage/images/logo.png') }}" alt="Bowlance" class="size-16">
            </a>
        </div>

        <!-- Центральная часть - иконки -->
        <div class="navbar-center hidden md:flex gap-2 md:gap-6 items-center">

            <!-- Телефон -->
            <a href="tel:+995555123456" class="flex items-center justify-center gap-3" aria-label="{{ __('frontend.phone') }}">
                <span class="icon-[tabler--phone] size-10 text-emerald-600 mr-3"></span>
                <div class="flex flex-col">                    
                    <span class="text-xs text-base-content/50">Заказать по телефону:</span>
                    <span class="text-base font-bold">+995 500 700 877</span>
                </div>
                
            </a>     
            <button type="button" class="flex items-center justify-center gap-3" aria-label="{{ __('frontend.location') }}">
                <span class="icon-[tabler--live-view] bg-amber-700 size-10"></span>
                <div class="flex flex-col items-start">                    
                    <span class="text-xs text-base-content/50">Пн-Вс 10:00-22:00</span>
                    <span class="text-base font-bold">{{ __('frontend.location') }}</span>
                </div>
            </button>

            <a href="https://instagram.com/bowlance.ge" target="_blank" class="flex items-center justify-center" aria-label="Instagram">
                <span class="icon-[tabler--brand-instagram] size-10 bg-linear-65 from-pink-400 to-purple-500"></span>
            </a>
        </div>

        <!-- Правая часть - язык и корзина -->        


        <div class="navbar-end flex items-center gap-3">
            <div class="dropdown relative inline-flex [--placement:bottom-end]">
              <button id="locale-dropdown" 
                      type="button" 
                      class="dropdown-toggle bg-transparent flex items-center justify-center gap-2 border border-emerald-600 rounded-full px-2 py-1" 
                      aria-haspopup="menu"
                      aria-expanded="false" 
                      aria-label="Выбрать язык">
                <!-- <span class="icon-[tabler--language] size-4"></span> -->
                <span class="text-sm">{{ strtoupper(app()->getLocale()) }}</span>
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

            @auth
            <div class="flex items-center justify-center">
                <a href="{{ route('cabinet.dashboard') }}" class="w-full h-full flex items-center justify-center" aria-label="Личный кабинет" title="Личный кабинет">
                    <span class="icon-[tabler--user] size-5"></span>
                </a>
            </div>
            @else
            <div class="flex items-center justify-center">
                <a href="{{ route('cabinet.login') }}" class="text-emerald-600"  aria-label="Личный кабинет" title="Личный кабинет">
                    <span class="icon-[tabler--user] size-6"></span>
                </a>
            </div>
            @endauth

            
            <!-- Offcanvas Меню -->

            <button type="button" class="flex md:hidden items-center justify-center" aria-haspopup="dialog" aria-expanded="false" aria-controls="overlay-end-example" data-overlay="#overlay-end-example">
                <span class="icon-[tabler--baseline-density-medium] size-5"></span>
            </button>

            <!-- Корзина -->
            <button type="button" 
                    class="btn btn-circle btn-primary btn-sm gap-2 relative" 
                    aria-label="{{ __('frontend.cart') }}" 
                    @click="$store.cart.openDrawer()"
                    x-data>
                <span class="icon-[tabler--shopping-cart] size-5"></span>
                <span class="badge  badge-sm absolute -top-1 -right-1" 
                      x-show="$store.cart.totalItems > 0"
                      x-text="$store.cart.totalItems"
                      x-cloak></span>
            </button>


            
        </div>
    </div>
</header>

<!-- Отступ для fixed header -->
<div class="h-16"></div>




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
                    <h3 class="font-bold text-lg">{{ __('frontend.cart_title') }}: </h3>
                    <p class="text-sm text-base-content/60" x-show="$store.cart.totalItems > 0" x-cloak>
                        <span x-text="$store.cart.totalItems"></span> 
                        <span x-text="$store.cart.totalItems === 1 ? '{{ __('frontend.items_single') }}' : ($store.cart.totalItems < 5 ? '{{ __('frontend.items_few') }}' : '{{ __('frontend.items_many') }}')"></span>
                    </p>
                </div>
                <!-- Кнопка очистить корзину -->
                <button type="button" 
                        @click="$store.cart.clearCart()"
                        class=" gap-2">
                    <span class="icon-[tabler--trash] size-4"></span>
                    {{ __('frontend.clear_cart') }}
                </button>
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
                                    <template x-if="item.type === 'drink' && item.image">
                                        <img :src="'/storage/' + item.image" 
                                             :alt="item.name" 
                                             class="size-20 rounded-lg object-cover">
                                    </template>
                                    <template x-if="item.type === 'drink' && !item.image">
                                        <div class="size-20 rounded-lg bg-base-300 flex items-center justify-center">
                                            <span class="icon-[tabler--cup] size-8 text-base-content/30"></span>
                                        </div>
                                    </template>
                                    <template x-if="item.type === 'bowl'">
                                        <div class="size-20 rounded-lg bg-primary/20 flex items-center justify-center">
                                            <img src="{{ asset('storage/constructor-products/bowl-constructor.jpg') }}" alt="Bowl" class="size-20 rounded-lg object-cover">
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
                                            <template x-if="item.volume">
                                                <p class="text-xs text-base-content/50" x-text="item.volume"></p>
                                            </template>
                                        </div>
                                        <button type="button" 
                                                @click="$store.cart.removeItem(index)"
                                                class="btn btn-ghost btn-circle btn-xs"
                                                aria-label="{{ __('frontend.remove') }}">
                                            <span class="icon-[tabler--trash] size-4"></span>
                                        </button>
                                    </div>

                                    <!-- Пищевая ценность блюда (если есть) -->
                                    <template x-if="item.calories > 0 || item.proteins > 0 || item.fats > 0 || item.carbs > 0">
                                        <div class="mb-2">
                                            <p class="text-xs font-medium text-base-content/70 mb-1">Блюдо:</p>
                                            <div class="flex flex-wrap gap-1 text-xs">
                                                <template x-if="item.calories > 0">
                                                    <span class="badge badge-outline border-dashed badge-info badge-sm">
                                                        <span class="icon-[tabler--flame] mr-0.5 size-3"></span>
                                                        <span x-text="Math.round(item.calories)"></span> {{ __('frontend.calories') }}
                                                    </span>
                                                </template>
                                                <template x-if="item.proteins > 0">
                                                    <span class="badge badge-outline border-dashed badge-success badge-sm">
                                                        Б: <span x-text="item.proteins.toFixed(1)"></span>{{ __('frontend.grams') }}
                                                    </span>
                                                </template>
                                                <template x-if="item.fats > 0">
                                                    <span class="badge badge-outline border-dashed badge-warning badge-sm">
                                                        Ж: <span x-text="item.fats.toFixed(1)"></span>{{ __('frontend.grams') }}
                                                    </span>
                                                </template>
                                                <template x-if="item.carbs > 0">
                                                    <span class="badge badge-outline border-dashed badge-error badge-sm">
                                                        У: <span x-text="item.carbs.toFixed(1)"></span>{{ __('frontend.grams') }}
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <!-- Информация о соусе (если есть) -->
                                    <template x-if="item.sauce_name">
                                        <div class="mb-2 rounded bg-primary/5 p-2">
                                            <div class="flex items-center gap-1 mb-1">
                                                <span class="icon-[tabler--bottle] size-3 text-primary"></span>
                                                <span class="text-xs font-medium text-primary">+ <span x-text="item.sauce_name"></span></span>
                                                <template x-if="item.sauce_weight">
                                                    <span class="text-xs text-base-content/40">(<span x-text="item.sauce_weight"></span>)</span>
                                                </template>
                                            </div>
                                            <template x-if="item.sauce_calories > 0 || item.sauce_proteins > 0 || item.sauce_fats > 0 || item.sauce_carbs > 0">
                                                <div class="flex flex-wrap gap-1 text-xs">
                                                    <template x-if="item.sauce_calories > 0">
                                                        <span class="badge badge-outline badge-xs">
                                                            <span x-text="Math.round(item.sauce_calories)"></span> {{ __('frontend.calories') }}
                                                        </span>
                                                    </template>
                                                    <template x-if="item.sauce_proteins > 0">
                                                        <span class="badge badge-outline badge-xs">
                                                            Б: <span x-text="item.sauce_proteins.toFixed(1)"></span>{{ __('frontend.grams') }}
                                                        </span>
                                                    </template>
                                                    <template x-if="item.sauce_fats > 0">
                                                        <span class="badge badge-outline badge-xs">
                                                            Ж: <span x-text="item.sauce_fats.toFixed(1)"></span>{{ __('frontend.grams') }}
                                                        </span>
                                                    </template>
                                                    <template x-if="item.sauce_carbs > 0">
                                                        <span class="badge badge-outline badge-xs">
                                                            У: <span x-text="item.sauce_carbs.toFixed(1)"></span>{{ __('frontend.grams') }}
                                                        </span>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    
                                    <!-- Итоговая КБЖУ (если есть соус) -->
                                    <template x-if="item.sauce_name && (item.calories > 0 || item.sauce_calories > 0)">
                                        <div class="mb-2 border-t border-base-content/10 pt-2">
                                            <p class="text-xs font-semibold text-base-content/80 mb-1">Итого:</p>
                                            <div class="flex flex-wrap gap-1 text-xs">
                                                <span class="badge badge-primary badge-xs">
                                                    <span class="icon-[tabler--flame] mr-0.5 size-3"></span>
                                                    <span x-text="Math.round((item.calories || 0) + (item.sauce_calories || 0))"></span> {{ __('frontend.calories') }}
                                                </span>
                                                <span class="badge badge-primary badge-xs">
                                                    Б: <span x-text="((item.proteins || 0) + (item.sauce_proteins || 0)).toFixed(1)"></span>{{ __('frontend.grams') }}
                                                </span>
                                                <span class="badge badge-primary badge-xs">
                                                    Ж: <span x-text="((item.fats || 0) + (item.sauce_fats || 0)).toFixed(1)"></span>{{ __('frontend.grams') }}
                                                </span>
                                                <span class="badge badge-primary badge-xs">
                                                    У: <span x-text="((item.carbs || 0) + (item.sauce_carbs || 0)).toFixed(1)"></span>{{ __('frontend.grams') }}
                                                </span>
                                            </div>
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
     x-on:keydown.esc.prevent="handleEsc()">
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
            x-on:click.away="() => {}"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="flex min-h-screen items-center justify-center p-4"
        >
            <div class="w-full max-w-lg rounded-lg bg-base-100 p-6 shadow-xl max-h-[90vh] overflow-y-auto">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-xl font-bold">{{ __('frontend.checkout_title') }}</h3>
                    <button @click="closeModal()" class="btn btn-circle btn-ghost btn-sm">
                        <span class="icon-[tabler--x] size-5"></span>
                    </button>
                </div>

                <form @submit.prevent="submitOrder">
                    <div class="space-y-4">
                        <!-- Шаг 1: Основная информация -->
                        <div x-show="step === 1" class="space-y-3">
                            <!-- Имя -->
                            <div>
                                <label class="label">
                                    <span class="label-text font-bold">{{ __('frontend.your_name') }} <span class="text-error">*</span></span>
                                </label>
                                <input type="text" 
                                       x-model="formData.name" 
                                       class="input input-bordered w-full" 
                                       required
                                       placeholder="{{ __('frontend.name_placeholder') }}">                                 
                            </div>
                            

                            <!-- Телефон -->
                            <div>
                                <label class="label">
                                    <span class="label-text font-bold">{{ __('frontend.phone') }} <span class="text-error">*</span></span>
                                </label>
                                <input type="tel" 
                                       x-model="formData.phone" 
                                       class="input input-bordered w-full" 
                                       required
                                       placeholder="+995555123456"
                                       :disabled="phoneVerified"
                                       @input="phoneVerified = false; verificationRequestId = null">
                                <div x-show="phoneVerified" class="mt-2">
                                    <div class="alert alert-success">
                                        <span class="icon-[tabler--check] size-5"></span>
                                        <span class="text-sm">Номер верифицирован</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <!-- <div>
                                <label class="label">
                                    <span class="label-text">{{ __('frontend.email') }}</span>
                                </label>
                                <input type="email" 
                                       x-model="formData.email" 
                                       class="input input-bordered w-full" 
                                       placeholder="{{ __('frontend.email_placeholder') }}">
                            </div> -->

                            <!-- Тип доставки -->
                            <div class="mt-4">
                                <label class="label mb-2">
                                    <span class="label-text font-bold mb-2">Способ получения <span class="text-error">*</span></span>
                                </label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" 
                                               x-model="formData.deliveryType" 
                                               value="delivery" 
                                               name="delivery_type" 
                                               class="radio radio-primary">
                                        <span>Доставка</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" 
                                               x-model="formData.deliveryType" 
                                               value="pickup" 
                                               name="delivery_type" 
                                               class="radio radio-primary">
                                        <span>Самовывоз</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Детали доставки -->
                            <div x-show="formData.deliveryType === 'delivery'" class="space-y-4">
                                <!-- Выбор сохраненного адреса для авторизованных -->
                                <div x-show="isAuthenticated && savedAddresses.length > 0">
                                    <label class="label">
                                        <span class="label-text">Выберите сохраненный адрес</span>
                                    </label>
                                    <select x-model="selectedAddressId" 
                                            @change="loadAddress()"
                                            class="select select-bordered w-full">
                                        <option value="">Ввести новый адрес</option>
                                        <template x-for="addr in savedAddresses" :key="addr.id">
                                            <option :value="addr.id" x-text="addr.label + ': ' + addr.address"></option>
                                        </template>
                                    </select>
                                    <span class="helper-text">Please write your full name</span>
                                </div>

                                <!-- Выбор сохраненного адреса для гостей -->
                                <div x-show="!isAuthenticated && guestAddresses.length > 0">
                                    <label class="label">
                                        <span class="label-text">Выберите сохраненный адрес</span>
                                    </label>
                                    <select x-model="selectedGuestAddressIndex" 
                                            @change="loadGuestAddress()"
                                            class="select select-bordered w-full">
                                        <option value="">Ввести новый адрес</option>
                                        <template x-for="(addr, index) in guestAddresses" :key="index">
                                            <option :value="index" x-text="addr.address"></option>
                                        </template>
                                    </select>
                                    <span class="helper-text">Please write your full name</span>
                                </div>

                                <!-- Основной адрес -->
                                <div>
                                    <label class="label">
                                        <span class="label-text">{{ __('frontend.delivery_address') }}</span>
                                    </label>
                                    <input type="text" 
                                           x-model="formData.address" 
                                           class="input input-bordered w-full" 
                                           placeholder="{{ __('frontend.address_placeholder') }}">
                                </div>

                                <!-- Подъезд и Этаж -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">
                                            <span class="label-text">Подъезд</span>
                                        </label>
                                        <input type="text" 
                                               x-model="formData.entrance" 
                                               class="input input-bordered w-full" 
                                               placeholder="1">
                                    </div>
                                    <div>
                                        <label class="label">
                                            <span class="label-text">Этаж</span>
                                        </label>
                                        <input type="text" 
                                               x-model="formData.floor" 
                                               class="input input-bordered w-full" 
                                               placeholder="5">
                                    </div>
                                </div>

                                <!-- Квартира и Домофон -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">
                                            <span class="label-text">Квартира</span>
                                        </label>
                                        <input type="text" 
                                               x-model="formData.apartment" 
                                               class="input input-bordered w-full" 
                                               placeholder="42">
                                    </div>
                                    <div>
                                        <label class="label">
                                            <span class="label-text">Домофон</span>
                                        </label>
                                        <input type="text" 
                                               x-model="formData.intercom" 
                                               class="input input-bordered w-full" 
                                               placeholder="42К">
                                    </div>
                                </div>

                                <!-- Комментарий курьеру -->
                                <div>
                                    <label class="label">
                                        <span class="label-text">Комментарий курьеру</span>
                                    </label>
                                    <textarea x-model="formData.courierComment" 
                                              class="textarea textarea-bordered w-full" 
                                              rows="2"
                                              placeholder="Например: позвоните за 5 минут"></textarea>
                                </div>

                                <!-- Телефон получателя -->
                                <!-- <div>
                                    <label class="label">
                                        <span class="label-text">Телефон получателя</span>
                                    </label>
                                    <input type="tel" 
                                           x-model="formData.receiverPhone" 
                                           class="input input-bordered w-full" 
                                           placeholder="+995 555 12 34 56">
                                </div> -->

                                <!-- Оставить у двери -->
                                <!-- <div class="form-control">
                                    <label class="label cursor-pointer justify-start gap-3">
                                        <input type="checkbox" 
                                               x-model="formData.leaveAtDoor" 
                                               class="toggle toggle-primary">
                                        <span class="label-text">Оставить у двери</span>
                                    </label>
                                </div> -->
                            </div>

                            <!-- Комментарий -->
                            <!-- <div>
                                <label class="label">
                                    <span class="label-text">{{ __('frontend.order_comment') }}</span>
                                </label>
                                <textarea x-model="formData.comment" 
                                          class="textarea textarea-bordered w-full" 
                                          rows="2"
                                          placeholder="{{ __('frontend.comment_placeholder') }}"></textarea>
                            </div> -->

                            <!-- Итоговая сумма -->
                            <div class="rounded-lg bg-base-200 p-4">
                                <div class="flex items-center justify-between text-lg font-bold">
                                    <span>{{ __('frontend.total_to_pay') }}</span>
                                    <span class="text-primary" x-text="$store.cart.totalPrice.toFixed(2) + ' ₾'"></span>
                                </div>
                            </div>

                            <!-- Кнопка далее -->
                            <button type="button" 
                                    @click="goToVerification()"
                                    class="btn btn-primary w-full gap-2 mt-4"
                                    :disabled="!formData.name || !formData.phone">
                                <span>Далее: Верификация телефона</span>
                                <span class="icon-[tabler--arrow-right] size-5"></span>
                            </button>
                        </div>

                        <!-- Шаг 2: Верификация телефона -->
                        <div x-show="step === 2">
                            <div class="mb-4">
                                <button type="button" 
                                        @click="step = 1" 
                                        class="btn btn-ghost btn-sm gap-2">
                                    <span class="icon-[tabler--arrow-left] size-4"></span>
                                    Назад
                                </button>
                            </div>

                            <div class="space-y-4">
                                <!-- Информация о номере -->
                                <div class="alert">
                                    <span class="icon-[tabler--info-circle] size-5"></span>
                                    <div class="text-sm">
                                        <p>На номер <strong x-text="formData.phone"></strong> будет отправлен код подтверждения</p>
                                    </div>
                                </div>

                                <!-- Кнопка отправки кода -->
                                <div x-show="!codeSent">
                                    <button type="button" 
                                            @click="sendVerificationCode()"
                                            class="btn btn-primary w-full gap-2"
                                            :disabled="sendingCode">
                                        <span x-show="!sendingCode" class="icon-[tabler--send] size-5"></span>
                                        <span x-show="sendingCode" class="loading loading-spinner loading-sm"></span>
                                        <span x-text="sendingCode ? 'Отправка...' : 'Отправить код'"></span>
                                    </button>
                                </div>

                                <!-- Поле ввода кода -->
                                <div x-show="codeSent && !phoneVerified">
                                    <label class="label">
                                        <span class="label-text">Введите 6-значный код <span class="text-error">*</span></span>
                                    </label>
                                    <input type="text" 
                                           x-model="verificationCode" 
                                           maxlength="6"
                                           class="input input-bordered w-full text-center text-2xl tracking-widest font-mono" 
                                           placeholder="••••••"
                                           @input="verificationCode = verificationCode.replace(/[^0-9]/g, '')"
                                           autofocus>
                                    
                                    <div x-show="verificationError" class="mt-2">
                                        <div class="alert alert-error">
                                            <span class="icon-[tabler--alert-circle] size-5"></span>
                                            <span class="text-sm" x-text="verificationError"></span>
                                        </div>
                                    </div>

                                    <button type="button" 
                                            @click="verifyCode()"
                                            class="btn btn-primary w-full gap-2 mt-4"
                                            :disabled="verificationCode.length !== 6 || verifyingCode">
                                        <span x-show="!verifyingCode" class="icon-[tabler--check] size-5"></span>
                                        <span x-show="verifyingCode" class="loading loading-spinner loading-sm"></span>
                                        <span x-text="verifyingCode ? 'Проверка...' : 'Подтвердить код'"></span>
                                    </button>

                                    <button type="button" 
                                            @click="resendCode()"
                                            class="btn btn-ghost btn-sm w-full mt-2">
                                        Отправить код повторно
                                    </button>
                                </div>

                                <!-- Успешная верификация -->
                                <div x-show="phoneVerified">
                                    <div class="alert alert-success">
                                        <span class="icon-[tabler--circle-check] size-6"></span>
                                        <div>
                                            <h4 class="font-bold">Номер верифицирован!</h4>
                                            <p class="text-sm">Теперь вы можете оформить заказ</p>
                                        </div>
                                    </div>

                                    <!-- Ошибка при оформлении заказа -->
                                    <div x-show="orderError" class="mt-3">
                                        <div class="alert alert-error">
                                            <span class="icon-[tabler--alert-circle] size-5"></span>
                                            <span class="text-sm" x-text="orderError"></span>
                                        </div>
                                    </div>

                                    <button type="submit" 
                                            class="btn btn-primary w-full gap-2 mt-4"
                                            :disabled="loading">
                                        <span x-show="!loading" class="icon-[tabler--check] size-5"></span>
                                        <span x-show="loading" class="loading loading-spinner loading-sm"></span>
                                        <span x-text="loading ? 'Оформление...' : 'Оформить заказ'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END Модальное окно -->
