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
            <a href="tel:+995555123456" class="btn btn-ghost btn-sm gap-2" aria-label="Телефон">
                <span class="icon-[tabler--phone] size-5"></span>
                <span>+995 555 123 456</span>
            </a>
            
            <a href="https://instagram.com/bowlance" target="_blank" class="btn btn-ghost btn-sm gap-2" aria-label="Instagram">
                <span class="icon-[tabler--brand-instagram] size-5"></span>
            </a>
            
            <button type="button" class="btn btn-ghost btn-sm gap-2" aria-label="Наше местоположение">
                <span class="icon-[tabler--map-pin] size-5"></span>
                <span>Тбилиси</span>
            </button>
        </div>

        <!-- Правая часть - язык и корзина -->        


        <div class="navbar-end flex items-center gap-2">
            <div class="dropdown relative inline-flex">
              <button id="dropdown-default" type="button" class="dropdown-toggle btn btn-primary" aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                lang            <span class="icon-[tabler--chevron-down] dropdown-open:rotate-180 size-4"></span>
              </button>
              <ul class="dropdown-menu dropdown-open:opacity-100 hidden min-w-60" role="menu" aria-orientation="vertical" aria-labelledby="dropdown-default">
                <li><a class="dropdown-item" href="#">ru</a></li>
                <li><a class="dropdown-item" href="#">ka</a></li>
              </ul>
            </div>
            

            <!-- Корзина -->
            <button type="button" 
                    class="btn btn-primary btn-sm gap-2 relative" 
                    aria-label="Корзина" 
                    @click="$store.cart.openDrawer()"
                    x-data>
                <span class="icon-[tabler--shopping-cart] size-5"></span>
                <span class="badge badge-secondary badge-sm absolute -top-1 -right-1" 
                      x-show="$store.cart.totalItems > 0"
                      x-text="$store.cart.totalItems"
                      x-cloak></span>
            </button>

            <!-- Меню для мобильных -->
            <button type="button" class="btn btn-ghost btn-square btn-sm lg:hidden" data-hs-overlay="#mobileMenu" aria-label="Открыть меню">
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
            <h3 class="font-bold text-lg">Меню</h3>
            <button type="button" class="btn btn-text btn-circle btn-sm" aria-label="Закрыть" data-hs-overlay="#mobileMenu">
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
                    <span>Тбилиси</span>
                </button>
            </div>
        </div>
    </div>
</div>


<
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
                    <h3 class="font-bold text-lg">Корзина</h3>
                    <p class="text-sm text-base-content/60" x-show="$store.cart.totalItems > 0" x-cloak>
                        <span x-text="$store.cart.totalItems"></span> 
                        <span x-text="$store.cart.totalItems === 1 ? 'товар' : ($store.cart.totalItems < 5 ? 'товара' : 'товаров')"></span>
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
                <p class="text-base-content/60 mb-2 text-lg">Ваша корзина пуста</p>
                <p class="text-base-content/40 text-sm">Добавьте блюда или создайте свой боул</p>
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
                                                    <span x-text="item.products.length"></span> ингредиента
                                                </p>
                                            </template>
                                            <template x-if="item.weight">
                                                <p class="text-xs text-base-content/50" x-text="item.weight"></p>
                                            </template>
                                        </div>
                                        <button type="button" 
                                                @click="$store.cart.removeItem(index)"
                                                class="btn btn-ghost btn-circle btn-xs"
                                                aria-label="Удалить">
                                            <span class="icon-[tabler--trash] size-4"></span>
                                        </button>
                                    </div>

                                    <!-- Пищевая ценность (если есть) -->
                                    <template x-if="item.calories > 0">
                                        <div class="flex flex-wrap gap-1 mb-2 text-xs">
                                            <span class="badge badge-outline badge-xs">
                                                <span class="icon-[tabler--flame] mr-0.5 size-3"></span>
                                                <span x-text="Math.round(item.calories)"></span> ккал
                                            </span>
                                            <template x-if="item.proteins > 0">
                                                <span class="badge badge-outline badge-xs">
                                                    Б: <span x-text="item.proteins.toFixed(1)"></span>г
                                                </span>
                                            </template>
                                            <template x-if="item.fats > 0">
                                                <span class="badge badge-outline badge-xs">
                                                    Ж: <span x-text="item.fats.toFixed(1)"></span>г
                                                </span>
                                            </template>
                                            <template x-if="item.carbs > 0">
                                                <span class="badge badge-outline badge-xs">
                                                    У: <span x-text="item.carbs.toFixed(1)"></span>г
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
                                        Состав боула
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
                    Очистить корзину
                </button>
            </div>
            </div>
            <!-- END Content -->

            <!-- Footer - всегда прижат к низу -->
            <div class="flex-none border-t border-base-content/10 bg-base-100" x-show="$store.cart.items.length > 0" x-cloak>
                <div class="p-4">
            <!-- Общая пищевая ценность -->
            <div class="mb-4 rounded-lg bg-base-200/50 p-3">
                <p class="text-xs font-medium mb-2 text-base-content/70">Пищевая ценность:</p>
                <div class="grid grid-cols-4 gap-2 text-center">
                    <div>
                        <p class="text-xs text-base-content/50">Ккал</p>
                        <p class="text-sm font-bold" x-text="Math.round($store.cart.totalNutrition.calories)"></p>
                    </div>
                    <div>
                        <p class="text-xs text-base-content/50">Белки</p>
                        <p class="text-sm font-bold" x-text="$store.cart.totalNutrition.proteins.toFixed(1) + 'г'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-base-content/50">Жиры</p>
                        <p class="text-sm font-bold" x-text="$store.cart.totalNutrition.fats.toFixed(1) + 'г'"></p>
                    </div>
                    <div>
                        <p class="text-xs text-base-content/50">Углев.</p>
                        <p class="text-sm font-bold" x-text="$store.cart.totalNutrition.carbs.toFixed(1) + 'г'"></p>
                    </div>
                </div>
            </div>

            <!-- Итоговая сумма -->
            <div class="mb-4 flex items-center justify-between">
                <span class="text-lg font-medium">Итого:</span>
                <span class="text-2xl font-bold text-primary" x-text="$store.cart.totalPrice.toFixed(2) + ' ₾'"></span>
            </div>

            <!-- Кнопка оформления -->
            <button type="button" 
                    class="btn btn-primary w-full gap-2"
                    :disabled="$store.cart.items.length === 0">
                <span class="icon-[tabler--check] size-5"></span>
                Оформить заказ
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
