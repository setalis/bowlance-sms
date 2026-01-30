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
                    <p class="text-base-content/80">{{ auth()->user()->phone ?? auth()->user()->email }}</p>
                </div>
            </div>
            <div class="h-full overflow-y-auto">
                <ul class="accordion menu menu-sm gap-1 p-3">
                    <li>
                        <a href="{{ route('cabinet.dashboard') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('cabinet.dashboard')])>
                            <span class="icon-[tabler--dashboard] size-4.5"></span>
                            <span class="grow">Главная</span>
                        </a>
                    </li>
                    <li
                        class="text-base-content/50 before:bg-base-content/20 mt-2 p-2 text-xs uppercase before:absolute before:-start-3 before:top-1/2 before:h-0.5 before:w-2.5">
                        Аккаунт</li>
                    <li>
                        <a href="{{ route('cabinet.profile.edit') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('cabinet.profile.*')])>
                            <span class="icon-[tabler--user] size-4.5"></span>
                            <span class="grow">Мои данные</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('cabinet.addresses.index') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('cabinet.addresses.*')])>
                            <span class="icon-[tabler--map-pin] size-4.5"></span>
                            <span class="grow">Мои адреса</span>
                        </a>
                    </li>
                    <li
                        class="text-base-content/50 before:bg-base-content/20 mt-2 p-2 text-xs uppercase before:absolute before:-start-3 before:top-1/2 before:h-0.5 before:w-2.5">
                        Заказы</li>
                    <li>
                        <a href="{{ route('cabinet.orders.index') }}" @class(['inline-flex w-full items-center px-2', 'menu-active' => request()->routeIs('cabinet.orders.*')])>
                            <span class="icon-[tabler--shopping-cart] size-4.5"></span>
                            <span class="grow">Мои заказы</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</aside>
