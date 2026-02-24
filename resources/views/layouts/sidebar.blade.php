<aside id="layout-sidebar"
    class="overlay overlay-open:translate-x-0 drawer drawer-start sm:w-75 inset-y-0 start-0 hidden h-full [--auto-close:lg] lg:z-50 lg:block lg:translate-x-0 lg:shadow-none"
    aria-label="Sidebar" tabindex="-1">
    <div class="drawer-body border-base-content/20 h-full border-e p-0">
        <div class="flex h-full max-h-full flex-col">
            <button type="button" class="btn btn-text btn-circle btn-sm absolute end-3 top-3 lg:hidden"
                aria-label="Close" data-overlay="#layout-sidebar">
                <span class="icon-[tabler--x] size-4.5"></span>
            </button>
            <div class="text-base-content border-base-content/20 flex flex-col items-center gap-4 border-b px-4 py-6">
                <div class="avatar">
                    <div class="size-17 rounded-full">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random"
                            alt="avatar" />
                    </div>
                </div>
                <div class="text-center">
                    <h3 class="text-base-content text-lg font-semibold">{{ auth()->user()->name }}</h3>
                    <p class="text-base-content/80">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <div class="h-full overflow-y-auto">
                <ul class="accordion menu menu-sm gap-1 p-3">
                    <!-- Dashboard Menu Item -->
                    <li>
                        <a href="{{ route('admin.dashboard') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.dashboard')])>
                            <span class="icon-[tabler--dashboard] size-4.5"></span>
                            <span class="grow">Dashboard</span>
                        </a>
                    </li>

                    <!-- Section Divider -->
                    <li
                        class="text-base-content/50 before:bg-base-content/20 mt-2 p-2 text-xs uppercase before:absolute before:-start-3 before:top-1/2 before:h-0.5 before:w-2.5">
                        Контент</li>

                    <!-- Categories Management Menu -->
                    <li @class(['accordion-item', 'active' => request()->routeIs('admin.categories.*')]) id="categories-management">
                        <button
                            class="accordion-toggle accordion-item-active:bg-neutral/10 inline-flex w-full items-center p-2 text-start text-sm font-normal"
                            aria-controls="categories-management-collapse" aria-expanded="true">
                            <span class="icon-[tabler--category] size-4.5"></span>
                            <span class="grow">Категории</span>
                            <span
                                class="icon-[tabler--chevron-right] accordion-item-active:rotate-90 size-4.5 shrink-0 transition-transform duration-300 rtl:rotate-180"></span>
                        </button>
                        <div id="categories-management-collapse"
                            class="accordion-content mt-1 hidden w-full overflow-hidden transition-[height] duration-300"
                            aria-labelledby="categories-management" role="region" @if(request()->routeIs('admin.categories.*')) style="display: block;" @endif>
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('admin.categories.index') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.categories.index')])>
                                        <span>Все категории</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.categories.create') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.categories.create')])>
                                        <span>Добавить категорию</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Dishes Management Menu -->
                    <li @class(['accordion-item', 'active' => request()->routeIs('admin.dishes.*')]) id="dishes-management">
                        <button
                            class="accordion-toggle accordion-item-active:bg-neutral/10 inline-flex w-full items-center p-2 text-start text-sm font-normal"
                            aria-controls="dishes-management-collapse" aria-expanded="true">
                            <span class="icon-[tabler--tools-kitchen-2] size-4.5"></span>
                            <span class="grow">Блюда</span>
                            <span
                                class="icon-[tabler--chevron-right] accordion-item-active:rotate-90 size-4.5 shrink-0 transition-transform duration-300 rtl:rotate-180"></span>
                        </button>
                        <div id="dishes-management-collapse"
                            class="accordion-content mt-1 hidden w-full overflow-hidden transition-[height] duration-300"
                            aria-labelledby="dishes-management" role="region" @if(request()->routeIs('admin.dishes.*')) style="display: block;" @endif>
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('admin.dishes.index') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.dishes.index')])>
                                        <span>Все блюда</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.dishes.create') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.dishes.create')])>
                                        <span>Добавить блюдо</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Drinks Management Menu -->
                    <li @class(['accordion-item', 'active' => request()->routeIs('admin.drinks.*')]) id="drinks-management">
                        <button
                            class="accordion-toggle accordion-item-active:bg-neutral/10 inline-flex w-full items-center p-2 text-start text-sm font-normal"
                            aria-controls="drinks-management-collapse" aria-expanded="true">
                            <span class="icon-[tabler--cup] size-4.5"></span>
                            <span class="grow">Напитки</span>
                            <span
                                class="icon-[tabler--chevron-right] accordion-item-active:rotate-90 size-4.5 shrink-0 transition-transform duration-300 rtl:rotate-180"></span>
                        </button>
                        <div id="drinks-management-collapse"
                            class="accordion-content mt-1 hidden w-full overflow-hidden transition-[height] duration-300"
                            aria-labelledby="drinks-management" role="region" @if(request()->routeIs('admin.drinks.*')) style="display: block;" @endif>
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('admin.drinks.index') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.drinks.index')])>
                                        <span>Все напитки</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.drinks.create') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.drinks.create')])>
                                        <span>Добавить напиток</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Section Divider -->
                    <li
                        class="text-base-content/50 before:bg-base-content/20 mt-2 p-2 text-xs uppercase before:absolute before:-start-3 before:top-1/2 before:h-0.5 before:w-2.5">
                        Заказы</li>

                    <!-- Orders Management Menu -->
                    <li @class(['accordion-item', 'active' => request()->routeIs('admin.orders.*')]) id="orders-management">
                        <button
                            class="accordion-toggle accordion-item-active:bg-neutral/10 inline-flex w-full items-center p-2 text-start text-sm font-normal"
                            aria-controls="orders-management-collapse" aria-expanded="true">
                            <span class="icon-[tabler--shopping-cart] size-4.5"></span>
                            <span class="grow">Управление заказами</span>
                            <span
                                class="icon-[tabler--chevron-right] accordion-item-active:rotate-90 size-4.5 shrink-0 transition-transform duration-300 rtl:rotate-180"></span>
                        </button>
                        <div id="orders-management-collapse"
                            class="accordion-content mt-1 hidden w-full overflow-hidden transition-[height] duration-300"
                            aria-labelledby="orders-management" role="region" @if(request()->routeIs('admin.orders.*')) style="display: block;" @endif>
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('admin.orders.index') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.orders.index')])>
                                        <span>Все заказы</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.orders.create') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.orders.create')])>
                                        <span>Создать заказ</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Section Divider -->
                    <li
                        class="text-base-content/50 before:bg-base-content/20 mt-2 p-2 text-xs uppercase before:absolute before:-start-3 before:top-1/2 before:h-0.5 before:w-2.5">
                        Конструктор боула</li>

                    <!-- Constructor Categories Menu -->
                    <li @class(['accordion-item', 'active' => request()->routeIs('admin.constructor-categories.*')]) id="constructor-categories-management">
                        <button
                            class="accordion-toggle accordion-item-active:bg-neutral/10 inline-flex w-full items-center p-2 text-start text-sm font-normal"
                            aria-controls="constructor-categories-management-collapse" aria-expanded="true">
                            <span class="icon-[tabler--bowl-chopsticks] size-4.5"></span>
                            <span class="grow">Категории конструктора</span>
                            <span
                                class="icon-[tabler--chevron-right] accordion-item-active:rotate-90 size-4.5 shrink-0 transition-transform duration-300 rtl:rotate-180"></span>
                        </button>
                        <div id="constructor-categories-management-collapse"
                            class="accordion-content mt-1 hidden w-full overflow-hidden transition-[height] duration-300"
                            aria-labelledby="constructor-categories-management" role="region" @if(request()->routeIs('admin.constructor-categories.*')) style="display: block;" @endif>
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('admin.constructor-categories.index') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.constructor-categories.index')])>
                                        <span>Все категории</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.constructor-categories.create') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.constructor-categories.create')])>
                                        <span>Добавить категорию</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Constructor Products Menu -->
                    <li @class(['accordion-item', 'active' => request()->routeIs('admin.constructor-products.*')]) id="constructor-products-management">
                        <button
                            class="accordion-toggle accordion-item-active:bg-neutral/10 inline-flex w-full items-center p-2 text-start text-sm font-normal"
                            aria-controls="constructor-products-management-collapse" aria-expanded="true">
                            <span class="icon-[tabler--meat] size-4.5"></span>
                            <span class="grow">Продукты конструктора</span>
                            <span
                                class="icon-[tabler--chevron-right] accordion-item-active:rotate-90 size-4.5 shrink-0 transition-transform duration-300 rtl:rotate-180"></span>
                        </button>
                        <div id="constructor-products-management-collapse"
                            class="accordion-content mt-1 hidden w-full overflow-hidden transition-[height] duration-300"
                            aria-labelledby="constructor-products-management" role="region" @if(request()->routeIs('admin.constructor-products.*')) style="display: block;" @endif>
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('admin.constructor-products.index') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.constructor-products.index')])>
                                        <span>Все продукты</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.constructor-products.create') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.constructor-products.create')])>
                                        <span>Добавить продукт</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Section Divider -->
                    <li
                        class="text-base-content/50 before:bg-base-content/20 mt-2 p-2 text-xs uppercase before:absolute before:-start-3 before:top-1/2 before:h-0.5 before:w-2.5">
                        Скидки и акции</li>

                    <!-- Discounts Menu -->
                    <li @class(['accordion-item', 'active' => request()->routeIs('admin.discounts.*')]) id="discounts-management">
                        <button
                            class="accordion-toggle accordion-item-active:bg-neutral/10 inline-flex w-full items-center p-2 text-start text-sm font-normal"
                            aria-controls="discounts-management-collapse" aria-expanded="true">
                            <span class="icon-[tabler--discount] size-4.5"></span>
                            <span class="grow">Скидки</span>
                            <span
                                class="icon-[tabler--chevron-right] accordion-item-active:rotate-90 size-4.5 shrink-0 transition-transform duration-300 rtl:rotate-180"></span>
                        </button>
                        <div id="discounts-management-collapse"
                            class="accordion-content mt-1 hidden w-full overflow-hidden transition-[height] duration-300"
                            aria-labelledby="discounts-management" role="region" @if(request()->routeIs('admin.discounts.*')) style="display: block;" @endif>
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('admin.discounts.index') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.discounts.index')])>
                                        <span>Все скидки</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.discounts.create') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.discounts.create')])>
                                        <span>Добавить скидку</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>

                    <!-- Section Divider -->
                    <li
                        class="text-base-content/50 before:bg-base-content/20 mt-2 p-2 text-xs uppercase before:absolute before:-start-3 before:top-1/2 before:h-0.5 before:w-2.5">
                        Управление</li>

                    <!-- Parameters -->
                    <li>
                        <a href="{{ route('admin.parameters.index') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.parameters.*')])>
                            <span class="icon-[tabler--settings] size-4.5"></span>
                            <span class="grow">Параметры</span>
                        </a>
                    </li>

                    <!-- User Management Menu -->
                    <li @class(['accordion-item', 'active' => request()->routeIs('admin.users.*')]) id="user-management">
                        <button
                            class="accordion-toggle accordion-item-active:bg-neutral/10 inline-flex w-full items-center p-2 text-start text-sm font-normal"
                            aria-controls="user-management-collapse-user-management" aria-expanded="true">
                            <span class="icon-[tabler--users] size-4.5"></span>
                            <span class="grow">Пользователи</span>
                            <span
                                class="icon-[tabler--chevron-right] accordion-item-active:rotate-90 size-4.5 shrink-0 transition-transform duration-300 rtl:rotate-180"></span>
                        </button>
                        <div id="user-management-collapse-user-management"
                            class="accordion-content mt-1 hidden w-full overflow-hidden transition-[height] duration-300"
                            aria-labelledby="account-settings" role="region" @if(request()->routeIs('admin.users.*')) style="display: block;" @endif>
                            <ul class="space-y-1">
                                <li>
                                    <a href="{{ route('admin.users.index') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('admin.users.index')])>
                                        <span>Все пользователи</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</aside>
