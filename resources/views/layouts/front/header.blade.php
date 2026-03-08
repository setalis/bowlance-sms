<header class="navbar h-20 fixed z-50 border-b border-base-content/10 {{ !$siteOrdersEnabled ? 'top-14' : 'top-0' }}">
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

            <!-- Корзина (скрыта при техработах) -->
            @if($siteOrdersEnabled)
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
            @endif


            
        </div>
    </div>
</header>

<!-- Отступ для fixed header (учитываем баннер техработ при его показе) -->
<div class="{{ $siteOrdersEnabled ? 'h-16' : 'h-[8.5rem]' }}"></div>




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
        class="z-90 fixed inset-0 overflow-hidden bg-black/50 backdrop-blur-sm"
    >
        <!-- Offcanvas Sidebar -->
        <div
            x-cloak
            x-show="$store.cart.isOpen"
            x-on:click.away="$store.cart.isOpen = false"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full rtl:-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full rtl:-translate-x-full"
            role="document"
            class="absolute flex flex-col bg-base-100 shadow-2xl will-change-transform h-dvh top-0 end-0 w-full sm:max-w-md"
        >
            <!-- Header -->
            <div class="flex flex-none items-center justify-between px-5 py-4 border-b border-base-200">
                <div class="flex items-center gap-3">
                    <div class="size-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center shrink-0">
                        <span class="icon-[tabler--shopping-cart] size-5 text-emerald-600"></span>
                    </div>
                    <div>
                        <h3 class="font-bold text-base leading-tight">{{ __('frontend.cart_title') }}</h3>
                        <p class="text-xs text-base-content/50 leading-tight" x-show="$store.cart.totalItems > 0" x-cloak>
                            <span x-text="$store.cart.totalItems"></span>&nbsp;<span x-text="$store.cart.totalItems === 1 ? '{{ __('frontend.items_single') }}' : ($store.cart.totalItems < 5 ? '{{ __('frontend.items_few') }}' : '{{ __('frontend.items_many') }}')"></span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button type="button"
                            @click="$store.cart.clearCart()"
                            class="btn btn-sm btn-circle  text-base-content/60 hover:text-error bg-white hover:bg-error/10"
                            title="{{ __('frontend.clear_cart') }}">
                        <span class="icon-[tabler--trash] size-4"></span>
                    </button>
                    <button @click="$store.cart.isOpen = false" type="button" class="btn btn-ghost btn-sm btn-circle bg-white text-base-content/60">
                        <span class="icon-[tabler--x] size-5"></span>
                    </button>
                </div>
            </div>
            <!-- END Header -->

            <!-- Content -->
            <div class="flex grow flex-col overflow-y-auto px-4 py-4 gap-3">
                <!-- Пустая корзина -->
                <div x-show="$store.cart.items.length === 0"
                     x-cloak
                     class="flex flex-col items-center justify-center py-16 text-center gap-3">
                    <div class="size-20 rounded-2xl bg-base-200 flex items-center justify-center">
                        <span class="icon-[tabler--shopping-cart-off] size-10 text-base-content/25"></span>
                    </div>
                    <div>
                        <p class="font-semibold text-base-content/70 mb-1">{{ __('frontend.cart_empty') }}</p>
                        <p class="text-sm text-base-content/40">{{ __('frontend.cart_empty_desc') }}</p>
                    </div>
                </div>

                <!-- Список товаров -->
                <div x-show="$store.cart.items.length > 0" x-cloak class="space-y-3">
                    <template x-for="(item, index) in $store.cart.items" :key="index">
                        <div class="group bg-base-100 rounded-2xl border border-base-200 hover:border-emerald-200 hover:shadow-md transition-all duration-200 overflow-hidden"
                             x-data="{ showNutrition: false }">
                            <div class="p-3">
                                <div class="flex gap-3">
                                    <!-- Изображение -->
                                    <div class="shrink-0">
                                        <template x-if="item.type === 'dish' && item.image">
                                            <img :src="'/storage/' + item.image"
                                                 :alt="item.name"
                                                 class="size-16 rounded-xl object-cover">
                                        </template>
                                        <template x-if="item.type === 'dish' && !item.image">
                                            <div class="size-16 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 flex items-center justify-center">
                                                <span class="icon-[tabler--bowl-chopsticks] size-7 text-emerald-400"></span>
                                            </div>
                                        </template>
                                        <template x-if="item.type === 'drink' && item.image">
                                            <img :src="'/storage/' + item.image"
                                                 :alt="item.name"
                                                 class="size-16 rounded-xl object-cover">
                                        </template>
                                        <template x-if="item.type === 'drink' && !item.image">
                                            <div class="size-16 rounded-xl bg-sky-50 dark:bg-sky-950/30 flex items-center justify-center">
                                                <span class="icon-[tabler--cup] size-7 text-sky-400"></span>
                                            </div>
                                        </template>
                                        <template x-if="item.type === 'bowl'">
                                            <div class="size-16 rounded-xl overflow-hidden">
                                                <img src="{{ asset('storage/constructor-products/bowl-constructor.jpg') }}" alt="Bowl" class="size-16 object-cover">
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Информация -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-1 mb-1">
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-semibold text-sm leading-tight truncate" x-text="item.name"></h4>
                                                <template x-if="item.type === 'bowl' && item.products">
                                                    <p class="text-xs text-base-content/50 mt-0.5">
                                                        <span x-text="item.products.length"></span> {{ __('frontend.ingredients') }}
                                                    </p>
                                                </template>
                                                <template x-if="item.weight">
                                                    <p class="text-xs text-base-content/40 mt-0.5" x-text="item.weight"></p>
                                                </template>
                                                <template x-if="item.volume">
                                                    <p class="text-xs text-base-content/40 mt-0.5" x-text="item.volume"></p>
                                                </template>
                                                <!-- Соус -->
                                                <template x-if="item.sauce_name">
                                                    <p class="text-xs text-emerald-600 mt-0.5 flex items-center gap-1">
                                                        <span class="icon-[tabler--bottle] size-3"></span>
                                                        + <span x-text="item.sauce_name"></span>
                                                    </p>
                                                </template>
                                            </div>
                                            <button type="button"
                                                    @click="$store.cart.removeItem(index)"
                                                    class="btn btn-circle size-7 min-h-0 h-7 text-base-content/50 hover:text-error bg-white hover:bg-error/10 shrink-0"
                                                    aria-label="{{ __('frontend.remove') }}">
                                                <span class="icon-[tabler--x] size-3.5"></span>
                                            </button>
                                        </div>

                                        <!-- Нижняя строка: КБЖУ-тоггл + количество + цена -->
                                        <div class="flex items-center justify-between mt-2 gap-2">
                                            <!-- Кнопка КБЖУ -->
                                            <template x-if="item.calories > 0">
                                                <button type="button"
                                                        @click="showNutrition = !showNutrition"
                                                        class="flex items-center gap-1 text-md text-base-content/40 hover:text-emerald-600 transition-colors">
                                                    <span class="icon-[tabler--flame] size-5"></span>
                                                    <span x-text="Math.round((item.calories || 0) + (item.sauce_calories || 0))"></span>
                                                    <span>{{ __('frontend.calories') }}</span>
                                                    <span class="icon-[tabler--chevron-down] size-5 transition-transform" :class="showNutrition ? 'rotate-180' : ''"></span>
                                                </button>
                                            </template>

                                            <!-- Управление количеством (pill) -->
                                            <div class="flex items-center gap-1.5 bg-base-200 rounded-full px-1.5 py-1 ml-auto">
                                                <button type="button"
                                                        @click="$store.cart.decreaseQuantity(index)"
                                                        :disabled="item.quantity <= 1"
                                                        class="size-6 rounded-full bg-base-100 shadow-sm flex items-center justify-center text-base-content/60 hover:text-error disabled:opacity-30 transition-colors">
                                                    <span class="icon-[tabler--minus] size-3"></span>
                                                </button>
                                                <span class="min-w-5 text-center text-xs font-bold tabular-nums" x-text="item.quantity"></span>
                                                <button type="button"
                                                        @click="$store.cart.increaseQuantity(index)"
                                                        class="size-6 rounded-full bg-emerald-500 text-white shadow-sm flex items-center justify-center hover:bg-emerald-600 transition-colors">
                                                    <span class="icon-[tabler--plus] size-3"></span>
                                                </button>
                                            </div>

                                            <!-- Цена -->
                                            <p class="font-bold text-sm text-primary tabular-nums shrink-0" x-text="(item.price * item.quantity).toFixed(2) + ' ₾'"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Раскрывающийся блок КБЖУ -->
                                <div x-show="showNutrition"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     class="mt-3 pt-3 border-t border-base-200">
                                    <div class="grid grid-cols-4 gap-2 text-center">
                                        <div class="bg-amber-50 dark:bg-amber-950/20 rounded-lg p-1.5">
                                            <p class="text-xs text-amber-600 font-bold" x-text="Math.round((item.calories||0)+(item.sauce_calories||0))"></p>
                                            <p class="text-xs text-base-content/40">{{ __('frontend.nutrition_calories') }}</p>
                                        </div>
                                        <div class="bg-emerald-50 dark:bg-emerald-950/20 rounded-lg p-1.5">
                                            <p class="text-xs text-emerald-600 font-bold" x-text="((item.proteins||0)+(item.sauce_proteins||0)).toFixed(1)+'{{ __('frontend.grams') }}'"></p>
                                            <p class="text-xs text-base-content/40">Б</p>
                                        </div>
                                        <div class="bg-orange-50 dark:bg-orange-950/20 rounded-lg p-1.5">
                                            <p class="text-xs text-orange-500 font-bold" x-text="((item.fats||0)+(item.sauce_fats||0)).toFixed(1)+'{{ __('frontend.grams') }}'"></p>
                                            <p class="text-xs text-base-content/40">Ж</p>
                                        </div>
                                        <div class="bg-blue-50 dark:bg-blue-950/20 rounded-lg p-1.5">
                                            <p class="text-xs text-blue-500 font-bold" x-text="((item.carbs||0)+(item.sauce_carbs||0)).toFixed(1)+'{{ __('frontend.grams') }}'"></p>
                                            <p class="text-xs text-base-content/40">У</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Состав боула -->
                                <template x-if="item.type === 'bowl' && item.products && item.products.length > 0">
                                    <details class="mt-3 pt-3 border-t border-base-200">
                                        <summary class="text-xs font-medium text-base-content/50 cursor-pointer hover:text-base-content/70 transition-colors list-none flex items-center gap-1">
                                            <span class="icon-[tabler--list] size-3"></span>
                                            {{ __('frontend.bowl_composition') }}
                                            <span class="icon-[tabler--chevron-down] size-3 ml-auto"></span>
                                        </summary>
                                        <ul class="mt-2 space-y-1">
                                            <template x-for="product in item.products" :key="product.id">
                                                <li class="flex items-center justify-between text-xs">
                                                    <span x-text="product.name" class="text-base-content/60"></span>
                                                    <span x-text="product.price.toFixed(2) + ' ₾'" class="text-base-content/40 tabular-nums"></span>
                                                </li>
                                            </template>
                                        </ul>
                                    </details>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Очистить корзину -->
                    <button type="button"
                            @click="$store.cart.clearCart()"
                            class="flex items-center justify-center gap-2 w-full py-2 text-xs text-base-content/40 hover:text-error transition-colors rounded-xl hover:bg-error/5">
                        <span class="icon-[tabler--trash] size-3.5"></span>
                        {{ __('frontend.clear_cart') }}
                    </button>
                </div>
            </div>
            <!-- END Content -->

            <!-- Footer -->
            <div class="flex-none bg-base-100" x-show="$store.cart.items.length > 0" x-cloak>
                <!-- КБЖУ итого -->
                <div class="px-5 pt-4 pb-2 border-t border-base-200">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-1 text-xs text-base-content/50">
                            <span class="icon-[tabler--flame] size-3 text-amber-500"></span>
                            <span x-text="Math.round($store.cart.totalNutrition.calories)"></span>
                            <span>{{ __('frontend.nutrition_calories') }}</span>
                        </div>
                        <div class="w-px h-3 bg-base-300"></div>
                        <div class="text-xs text-base-content/50">
                            Б <span x-text="$store.cart.totalNutrition.proteins.toFixed(1)" class="font-medium text-emerald-600"></span>
                        </div>
                        <div class="w-px h-3 bg-base-300"></div>
                        <div class="text-xs text-base-content/50">
                            Ж <span x-text="$store.cart.totalNutrition.fats.toFixed(1)" class="font-medium text-orange-500"></span>
                        </div>
                        <div class="w-px h-3 bg-base-300"></div>
                        <div class="text-xs text-base-content/50">
                            У <span x-text="$store.cart.totalNutrition.carbs.toFixed(1)" class="font-medium text-blue-500"></span>
                        </div>
                    </div>
                </div>

                <!-- Итог и кнопка -->
                <div class="px-4 pb-5 pt-3">
                    <div class="flex items-baseline justify-between mb-4">
                        <span class="text-sm text-base-content/60 font-medium">{{ __('frontend.total_price') }}</span>
                        <span class="text-3xl font-black text-emerald-600 tabular-nums" x-text="$store.cart.totalPrice.toFixed(2) + ' ₾'"></span>
                    </div>

                    @if($siteOrdersEnabled)
                        <button type="button"
                                class="w-full h-13 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold text-base flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-600/20 disabled:opacity-40 disabled:cursor-not-allowed"
                                :disabled="$store.cart.items.length === 0"
                                @click="$dispatch('open-checkout-modal')">
                            <span class="icon-[tabler--arrow-right] size-5"></span>
                            {{ __('frontend.checkout') }}
                        </button>
                    @else
                        <p class="text-warning text-center text-sm font-medium py-3">{{ __('frontend.orders_unavailable') }}</p>
                    @endif
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
        class="fixed inset-0 z-[100] overflow-y-auto bg-black/60 backdrop-blur-md flex items-center justify-center p-4"
    >
        <!-- Modal container -->
        <div
            x-cloak
            x-show="open"
            x-on:click.away="() => {}"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-2"
            class="w-full max-w-xl rounded-3xl bg-base-100 shadow-2xl flex flex-col max-h-[92vh]"
        >
            <!-- Sticky Header с прогресс-индикатором -->
            <div class="flex-none px-6 pt-5 pb-4 border-b border-base-200">
                <!-- Прогресс-индикатор шагов -->
                <div class="flex items-center gap-2 mb-4">
                    <div class="size-8 rounded-full flex items-center justify-center text-sm font-bold shrink-0 transition-all"
                         :class="step >= 1 ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'bg-base-200 text-base-content/40'">1</div>
                    <div class="flex-1 h-1 rounded-full overflow-hidden bg-base-200">
                        <div class="h-full bg-emerald-600 transition-all duration-500 rounded-full"
                             :style="step >= 2 ? 'width: 100%' : 'width: 0%'"></div>
                    </div>
                    <div class="size-8 rounded-full flex items-center justify-center text-sm font-bold shrink-0 transition-all"
                         :class="step >= 2 ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'bg-base-200 text-base-content/40'">2</div>
                </div>
                <!-- Заголовок + кнопка назад + закрыть -->
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <button type="button"
                                @click="step = 1"
                                x-show="step === 2"
                                class="btn btn-circle btn-sm text-base-content/50 bg-white hover:text-base-content -ml-1">
                            <span class="icon-[tabler--arrow-left] size-4"></span>
                        </button>
                        <div>
                            <h3 class="text-lg font-bold leading-tight"
                                x-text="step === 1 ? '{{ __('frontend.checkout_title') }}' : 'Подтверждение'"></h3>
                            <p class="text-xs text-base-content/40 leading-tight"
                               x-text="step === 1 ? 'Шаг 1 из 2 — контакты и доставка' : 'Шаг 2 из 2 — подтверждение номера'"></p>
                        </div>
                    </div>
                    <button @click="closeModal()" class="btn btn-circle btn-sm text-base-content/40 shrink-0 bg-white hover:bg-error/10">
                        <span class="icon-[tabler--x] size-5"></span>
                    </button>
                </div>
            </div>

            <!-- Scrollable body -->
            <div class="overflow-y-auto grow">
                <form @submit.prevent="submitOrder">

                    <!-- ====== ШАГ 1 ====== -->
                    <div x-show="step === 1" class="p-6 space-y-5">

                        <!-- Блок: Контакты -->
                        <div class="space-y-3">
                            <p class="text-xs font-semibold uppercase tracking-wider text-base-content/40">
                                <span class="icon-[tabler--user] size-3.5 mr-1 inline-block"></span>
                                Контактные данные
                            </p>
                            <!-- Имя -->
                            <div>
                                <label class="text-sm font-medium text-base-content/70 mb-1.5 block">
                                    {{ __('frontend.your_name') }} <span class="text-error">*</span>
                                </label>
                                <input type="text"
                                       x-model="formData.name"
                                       class="input input-bordered w-full rounded-xl focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/20"
                                       required
                                       placeholder="{{ __('frontend.name_placeholder') }}">
                            </div>
                            <!-- Телефон -->
                            <div>
                                <label class="text-sm font-medium text-base-content/70 mb-1.5 block">
                                    {{ __('frontend.phone') }} <span class="text-error">*</span>
                                </label>
                                <div class="relative">
                                    <input type="tel"
                                           x-model="formData.phone"
                                           class="input input-bordered w-full rounded-xl focus:border-emerald-400 focus:ring-2 focus:ring-emerald-400/20 pr-10"
                                           required
                                           placeholder="+995555123456"
                                           :disabled="phoneVerified"
                                           @input="phoneVerified = false; verificationRequestId = null">
                                    <span x-show="phoneVerified"
                                          x-cloak
                                          class="absolute right-3 top-1/2 -translate-y-1/2 icon-[tabler--circle-check-filled] size-5 text-emerald-500"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Блок: Способ получения -->
                        <div class="space-y-3">
                            <p class="text-xs font-semibold uppercase tracking-wider text-base-content/40">
                                <span class="icon-[tabler--map-pin] size-3.5 mr-1 inline-block"></span>
                                Способ получения <span class="text-error">*</span>
                            </p>
                            <div class="grid grid-cols-2 gap-3">
                                <!-- Доставка -->
                                <label class="flex items-center gap-3 p-3.5 rounded-2xl border-2 cursor-pointer transition-all"
                                       :class="formData.deliveryType === 'delivery' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/20' : 'border-base-200 hover:border-base-300'">
                                    <input type="radio"
                                           x-model="formData.deliveryType"
                                           value="delivery"
                                           name="delivery_type"
                                           class="hidden"
                                           @change="fetchWoltEstimate()">
                                    <div class="size-9 rounded-xl flex items-center justify-center shrink-0 transition-all"
                                         :class="formData.deliveryType === 'delivery' ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-base-200'">
                                        <span class="icon-[tabler--truck-delivery] size-5"
                                              :class="formData.deliveryType === 'delivery' ? 'text-emerald-600' : 'text-base-content/40'"></span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm leading-tight">Доставка</p>
                                        <p class="text-xs text-base-content/50 leading-tight">Wolt Drive</p>
                                    </div>
                                </label>
                                <!-- Самовывоз -->
                                <label class="flex items-center gap-3 p-3.5 rounded-2xl border-2 cursor-pointer transition-all"
                                       :class="formData.deliveryType === 'pickup' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/20' : 'border-base-200 hover:border-base-300'">
                                    <input type="radio"
                                           x-model="formData.deliveryType"
                                           value="pickup"
                                           name="delivery_type"
                                           class="hidden"
                                           @change="woltEstimate = { loading: false, available: null, fee: null, eta_minutes: null, message: null }">
                                    <div class="size-9 rounded-xl flex items-center justify-center shrink-0 transition-all"
                                         :class="formData.deliveryType === 'pickup' ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-base-200'">
                                        <span class="icon-[tabler--walk] size-5"
                                              :class="formData.deliveryType === 'pickup' ? 'text-emerald-600' : 'text-base-content/40'"></span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm leading-tight">Самовывоз</p>
                                        <p class="text-xs text-base-content/50 leading-tight">Из заведения</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Блок: Адрес доставки (только при delivery) -->
                        <div x-show="formData.deliveryType === 'delivery'" x-cloak class="space-y-3">
                            <p class="text-xs font-semibold uppercase tracking-wider text-base-content/40">
                                <span class="icon-[tabler--home] size-3.5 mr-1 inline-block"></span>
                                Адрес доставки
                            </p>

                            <!-- Сохранённые адреса (авторизованные) -->
                            <div x-show="isAuthenticated && savedAddresses.length > 0">
                                <label class="text-sm font-medium text-base-content/70 mb-1.5 block">Сохранённый адрес</label>
                                <select x-model="selectedAddressId"
                                        @change="loadAddress(); fetchWoltEstimate()"
                                        class="select select-bordered w-full rounded-xl focus:border-emerald-400">
                                    <option value="">Ввести новый адрес</option>
                                    <template x-for="addr in savedAddresses" :key="addr.id">
                                        <option :value="addr.id" x-text="addr.label + ': ' + addr.address"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Сохранённые адреса (гости) -->
                            <div x-show="!isAuthenticated && guestAddresses.length > 0">
                                <label class="text-sm font-medium text-base-content/70 mb-1.5 block">Сохранённый адрес</label>
                                <select x-model="selectedGuestAddressIndex"
                                        @change="loadGuestAddress(); fetchWoltEstimate()"
                                        class="select select-bordered w-full rounded-xl focus:border-emerald-400">
                                    <option value="">Ввести новый адрес</option>
                                    <template x-for="(addr, index) in guestAddresses" :key="index">
                                        <option :value="index" x-text="addr.address"></option>
                                    </template>
                                </select>
                            </div>

                            <!-- Город, Улица, Дом -->
                            <div class="bg-base-200/40 rounded-2xl p-4 space-y-3">
                                <div>
                                    <label class="text-xs font-medium text-base-content/60 mb-1 block">Город <span class="text-error">*</span></label>
                                    <input type="text"
                                           x-model="formData.deliveryCity"
                                           class="input input-bordered input-sm w-full rounded-xl bg-base-100 focus:border-emerald-400"
                                           placeholder="Батуми"
                                           @input.debounce.500ms="fetchWoltEstimate()"
                                           @change="fetchWoltEstimate()">
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-base-content/60 mb-1 block">Улица <span class="text-error">*</span></label>
                                    <input type="text"
                                           x-model="formData.deliveryStreet"
                                           class="input input-bordered input-sm w-full rounded-xl bg-base-100 focus:border-emerald-400"
                                           placeholder="ул. Парнаваз Мепе"
                                           @input.debounce.500ms="fetchWoltEstimate()"
                                           @change="fetchWoltEstimate()">
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-base-content/60 mb-1 block">Дом</label>
                                    <input type="text"
                                           x-model="formData.deliveryHouse"
                                           class="input input-bordered input-sm w-full rounded-xl bg-base-100 focus:border-emerald-400"
                                           placeholder="162/174"
                                           @input.debounce.500ms="fetchWoltEstimate()"
                                           @change="fetchWoltEstimate()">
                                </div>
                            </div>

                            <!-- Оценка Wolt -->
                            <template x-if="woltDeliveryEnabled && formData.deliveryCity.trim() && formData.deliveryStreet.trim()">
                                <div class="text-sm">
                                    <div x-show="woltEstimate.loading" class="flex items-center gap-2 text-base-content/50">
                                        <span class="loading loading-spinner loading-xs"></span>
                                        Проверяем доступность доставки…
                                    </div>
                                    <template x-if="!woltEstimate.loading && woltEstimate.available === true">
                                        <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/20 rounded-xl px-3 py-2">
                                            <span class="icon-[tabler--circle-check] size-4 shrink-0"></span>
                                            <span>
                                                <span x-show="woltEstimate.fee">Доставка <strong x-text="woltEstimate.fee ? woltEstimate.fee.amount.toFixed(2) + ' ₾' : ''"></strong></span>
                                                <span x-show="woltEstimate.eta_minutes" x-text="' · ~' + woltEstimate.eta_minutes + ' мин'"></span>
                                            </span>
                                        </div>
                                    </template>
                                    <div x-show="!woltEstimate.loading && woltEstimate.available === false"
                                         class="flex items-start gap-2 text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/20 rounded-xl px-3 py-2">
                                        <span class="icon-[tabler--info-circle] size-4 shrink-0 mt-0.5"></span>
                                        <div>
                                            <span x-show="woltEstimate.message" x-text="woltEstimate.message" class="text-xs block"></span>
                                            <span class="text-xs text-base-content/50">Можно оформить — доставка подтвердится при оформлении.</span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Детали квартиры -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-medium text-base-content/60 mb-1 block">Подъезд</label>
                                    <input type="text" x-model="formData.entrance" class="input input-bordered input-sm w-full rounded-xl focus:border-emerald-400" placeholder="1">
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-base-content/60 mb-1 block">Этаж</label>
                                    <input type="text" x-model="formData.floor" class="input input-bordered input-sm w-full rounded-xl focus:border-emerald-400" placeholder="5">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs font-medium text-base-content/60 mb-1 block">Квартира</label>
                                    <input type="text" x-model="formData.apartment" class="input input-bordered input-sm w-full rounded-xl focus:border-emerald-400" placeholder="42">
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-base-content/60 mb-1 block">Домофон</label>
                                    <input type="text" x-model="formData.intercom" class="input input-bordered input-sm w-full rounded-xl focus:border-emerald-400" placeholder="42К">
                                </div>
                            </div>

                            <!-- Комментарий курьеру -->
                            <div>
                                <label class="text-xs font-medium text-base-content/60 mb-1 block">Комментарий курьеру</label>
                                <textarea x-model="formData.courierComment"
                                          class="textarea textarea-bordered w-full rounded-xl text-sm focus:border-emerald-400"
                                          rows="2"
                                          placeholder="Например: позвоните за 5 минут"></textarea>
                            </div>
                        </div>

                        <!-- Итоговая сумма -->
                        <div class="rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100/60 dark:from-emerald-950/30 dark:to-emerald-900/10 border border-emerald-200/60 dark:border-emerald-800/40 p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-medium text-emerald-700 dark:text-emerald-300 mb-0.5">{{ __('frontend.total_to_pay') }}</p>
                                    <p x-show="formData.deliveryType === 'pickup' && pickupDiscount"
                                       x-cloak
                                       class="text-xs text-emerald-600/70 dark:text-emerald-400/70">
                                        Скидка за самовывоз применена
                                    </p>
                                </div>
                                <p class="text-3xl font-black text-emerald-700 dark:text-emerald-300 tabular-nums"
                                   x-text="totalToPay.toFixed(2) + ' ₾'"></p>
                            </div>
                        </div>

                        <!-- Кнопка далее -->
                        <button type="button"
                                @click="goToVerification()"
                                class="w-full h-13 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold text-base flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-600/20 disabled:opacity-40 disabled:cursor-not-allowed"
                                :disabled="!formData.name || !formData.phone">
                            Далее
                            <span class="icon-[tabler--arrow-right] size-5"></span>
                        </button>
                    </div>

                    <!-- ====== ШАГ 2 ====== -->
                    <div x-show="step === 2" class="p-6 space-y-4">

                        <!-- Номер телефона (не для callback) -->
                        <div x-show="verificationMethod !== 'callback'"
                             class="flex items-center gap-3 bg-base-200/50 rounded-2xl px-4 py-3">
                            <span class="icon-[tabler--phone] size-4 text-base-content/40 shrink-0"></span>
                            <p class="text-sm text-base-content/60">
                                Подтвердите номер <strong class="text-base-content font-semibold" x-text="formData.phone"></strong>
                            </p>
                        </div>

                        <!-- Карточки выбора метода -->
                        <div x-show="!codeSent && verificationMethod !== 'callback'" class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-wider text-base-content/40">Способ подтверждения</p>

                            @if(config('vonage.sms_enabled', true))
                            {{-- SMS верификация через Vonage (включается через VONAGE_SMS_ENABLED=true) --}}
                            <button type="button"
                                    @click="verificationMethod = 'sms'"
                                    class="flex items-center gap-4 p-4 rounded-2xl border-2 w-full text-left transition-all"
                                    :class="verificationMethod === 'sms' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/20' : 'border-base-200 hover:border-base-300'">
                                <div class="size-10 rounded-xl flex items-center justify-center shrink-0 transition-all"
                                     :class="verificationMethod === 'sms' ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-base-200'">
                                    <span class="icon-[tabler--message] size-5"
                                          :class="verificationMethod === 'sms' ? 'text-emerald-600' : 'text-base-content/40'"></span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-sm">SMS</p>
                                    <p class="text-xs text-base-content/50">Код придёт на ваш номер</p>
                                </div>
                                <span x-show="verificationMethod === 'sms'" class="icon-[tabler--circle-check-filled] size-5 text-emerald-500 shrink-0"></span>
                            </button>
                            @endif

                            <button type="button"
                                    @click="verificationMethod = 'telegram'"
                                    class="flex items-center gap-4 p-4 rounded-2xl border-2 w-full text-left transition-all"
                                    :class="verificationMethod === 'telegram' ? 'border-sky-500 bg-sky-50 dark:bg-sky-950/20' : 'border-base-200 hover:border-base-300'">
                                <div class="size-10 rounded-xl flex items-center justify-center shrink-0 transition-all"
                                     :class="verificationMethod === 'telegram' ? 'bg-sky-100 dark:bg-sky-900/40' : 'bg-base-200'">
                                    <span class="icon-[tabler--brand-telegram] size-5"
                                          :class="verificationMethod === 'telegram' ? 'text-sky-500' : 'text-base-content/40'"></span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-sm">Telegram</p>
                                    <p class="text-xs text-base-content/50">Получите код в боте</p>
                                </div>
                                <span x-show="verificationMethod === 'telegram'" class="icon-[tabler--circle-check-filled] size-5 text-sky-500 shrink-0"></span>
                            </button>

                            <button type="button"
                                    @click="verificationMethod = 'callback'"
                                    class="flex items-center gap-4 p-4 rounded-2xl border-2 w-full text-left transition-all"
                                    :class="verificationMethod === 'callback' ? 'border-amber-500 bg-amber-50 dark:bg-amber-950/20' : 'border-base-200 hover:border-base-300'">
                                <div class="size-10 rounded-xl flex items-center justify-center shrink-0 transition-all"
                                     :class="verificationMethod === 'callback' ? 'bg-amber-100 dark:bg-amber-900/40' : 'bg-base-200'">
                                    <span class="icon-[tabler--phone-call] size-5"
                                          :class="verificationMethod === 'callback' ? 'text-amber-600' : 'text-base-content/40'"></span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-sm">Звонок менеджера</p>
                                    <p class="text-xs text-base-content/50">Мы перезвоним для подтверждения</p>
                                </div>
                                <span x-show="verificationMethod === 'callback'" class="icon-[tabler--circle-check-filled] size-5 text-amber-500 shrink-0"></span>
                            </button>
                        </div>

                        @if(config('vonage.sms_enabled', true))
                        <!-- Кнопка отправки SMS -->
                        <div x-show="!codeSent && verificationMethod === 'sms'">
                            <button type="button"
                                    @click="sendVerificationCode()"
                                    class="w-full h-12 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold flex items-center justify-center gap-2 transition-all disabled:opacity-50"
                                    :disabled="sendingCode">
                                <span x-show="!sendingCode" class="icon-[tabler--send] size-5"></span>
                                <span x-show="sendingCode" class="loading loading-spinner loading-sm"></span>
                                <span x-text="sendingCode ? 'Отправка...' : 'Отправить SMS-код'"></span>
                            </button>
                        </div>
                        @endif

                        <!-- Telegram: кнопка открыть -->
                        <div x-show="!codeSent && verificationMethod === 'telegram'">
                            <button type="button"
                                    @click="startTelegramVerification()"
                                    class="w-full h-12 rounded-2xl bg-sky-500 hover:bg-sky-600 text-white font-semibold flex items-center justify-center gap-2 transition-all disabled:opacity-50"
                                    :disabled="sendingCode">
                                <span x-show="!sendingCode" class="icon-[tabler--brand-telegram] size-5"></span>
                                <span x-show="sendingCode" class="loading loading-spinner loading-sm"></span>
                                <span x-text="sendingCode ? 'Открытие...' : 'Открыть Telegram'"></span>
                            </button>
                        </div>

                        <!-- Telegram: повторно открыть после отправки -->
                        <div x-show="codeSent && !phoneVerified && verificationMethod === 'telegram' && telegramLink"
                             class="rounded-2xl bg-sky-50 dark:bg-sky-950/20 border border-sky-200 dark:border-sky-800 p-4">
                            <p class="text-sm text-sky-800 dark:text-sky-200 mb-3">
                                Получите код в Telegram-боте и введите его ниже.
                            </p>
                            <a :href="telegramLink"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="flex items-center justify-center gap-2 w-full h-9 rounded-xl border border-sky-300 dark:border-sky-700 text-sky-600 dark:text-sky-300 text-sm font-medium hover:bg-sky-100 dark:hover:bg-sky-900/30 transition-colors">
                                <span class="icon-[tabler--brand-telegram] size-4"></span>
                                Открыть Telegram снова
                            </a>
                        </div>

                        <!-- Поле ввода кода -->
                        <div x-show="codeSent && !phoneVerified && verificationMethod !== 'callback'" class="space-y-3">
                            <label class="text-sm font-medium text-base-content/70 block">
                                Введите 6-значный код <span class="text-error">*</span>
                            </label>
                            <input type="text"
                                   x-model="verificationCode"
                                   maxlength="6"
                                   class="input w-full text-center text-4xl tracking-[0.4em] font-black h-16 rounded-2xl border-2 border-base-200 focus:border-emerald-400 focus:ring-4 focus:ring-emerald-400/15 bg-base-100 font-mono"
                                   placeholder="——————"
                                   @input="verificationCode = verificationCode.replace(/[^0-9]/g, '')"
                                   autofocus>

                            <div x-show="verificationError"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="flex items-center gap-2 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800 rounded-xl px-3 py-2.5">
                                <span class="icon-[tabler--alert-circle] size-4 text-red-500 shrink-0"></span>
                                <span class="text-sm text-red-700 dark:text-red-300" x-text="verificationError"></span>
                            </div>

                            <button type="button"
                                    @click="verifyCode()"
                                    class="w-full h-12 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold flex items-center justify-center gap-2 transition-all disabled:opacity-40"
                                    :disabled="verificationCode.length !== 6 || verifyingCode">
                                <span x-show="!verifyingCode" class="icon-[tabler--check] size-5"></span>
                                <span x-show="verifyingCode" class="loading loading-spinner loading-sm"></span>
                                <span x-text="verifyingCode ? 'Проверка...' : 'Подтвердить код'"></span>
                            </button>

                            <button type="button"
                                    @click="resendCode()"
                                    class="flex items-center justify-center gap-1.5 w-full py-2 text-sm text-base-content/50 hover:text-base-content/70 transition-colors">
                                <span class="icon-[tabler--refresh] size-3.5"></span>
                                <span x-text="verificationMethod === 'telegram' ? 'Запросить новый код' : 'Отправить код повторно'"></span>
                            </button>
                        </div>

                        <!-- Callback: карточка + кнопка -->
                        <div x-show="verificationMethod === 'callback'" class="space-y-4">
                            <!-- Сменить метод (показываем всегда при callback) -->
                            <div x-show="verificationMethod === 'callback'"
                                 class="rounded-2xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 p-4">
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="size-10 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center shrink-0 mt-0.5">
                                        <span class="icon-[tabler--phone-call] size-5 text-amber-600"></span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-amber-800 dark:text-amber-200 text-sm">Менеджер перезвонит вам</p>
                                        <p class="text-xs text-amber-700/80 dark:text-amber-300/80 mt-0.5">
                                            Заказ будет создан, и наш менеджер позвонит на номер
                                            <strong x-text="formData.phone"></strong> для подтверждения.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div x-show="orderError"
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="flex items-center gap-2 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800 rounded-xl px-3 py-2.5">
                                <span class="icon-[tabler--alert-circle] size-4 text-red-500 shrink-0"></span>
                                <span class="text-sm text-red-700 dark:text-red-300" x-text="orderError"></span>
                            </div>

                            <button type="submit"
                                    class="w-full h-13 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold text-base flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-600/20 disabled:opacity-40"
                                    :disabled="loading">
                                <span x-show="!loading" class="icon-[tabler--check] size-5"></span>
                                <span x-show="loading" class="loading loading-spinner loading-sm"></span>
                                <span x-text="loading ? 'Оформление...' : 'Оформить заказ'"></span>
                            </button>

                            <button type="button"
                                    @click="verificationMethod = '{{ config('vonage.sms_enabled', true) ? 'sms' : 'telegram' }}'"
                                    class="flex items-center justify-center gap-1.5 w-full py-2 text-sm text-base-content/50 hover:text-base-content/70 transition-colors">
                                <span class="icon-[tabler--arrow-left] size-3.5"></span>
                                Выбрать другой способ
                            </button>
                        </div>

                        <!-- Успешная верификация -->
                        <div x-show="phoneVerified && verificationMethod !== 'callback'"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             class="space-y-4">
                            <div class="flex flex-col items-center text-center py-4">
                                <div class="size-16 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center mb-3">
                                    <span class="icon-[tabler--circle-check-filled] size-9 text-emerald-500"></span>
                                </div>
                                <h4 class="font-bold text-lg">Номер подтверждён!</h4>
                                <p class="text-sm text-base-content/50 mt-1">Вы можете оформить заказ</p>
                            </div>

                            <div x-show="orderError"
                                 class="flex items-center gap-2 bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-800 rounded-xl px-3 py-2.5">
                                <span class="icon-[tabler--alert-circle] size-4 text-red-500 shrink-0"></span>
                                <span class="text-sm text-red-700 dark:text-red-300" x-text="orderError"></span>
                            </div>

                            <button type="submit"
                                    class="w-full h-13 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:scale-[0.98] text-white font-bold text-base flex items-center justify-center gap-2 transition-all shadow-lg shadow-emerald-600/20 disabled:opacity-40"
                                    :disabled="loading">
                                <span x-show="!loading" class="icon-[tabler--check] size-5"></span>
                                <span x-show="loading" class="loading loading-spinner loading-sm"></span>
                                <span x-text="loading ? 'Оформление...' : 'Оформить заказ'"></span>
                            </button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- END Модальное окно -->
