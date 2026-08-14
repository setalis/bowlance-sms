@extends('layouts.front.app')

@section('content')
    <!-- Слайдер -->
    <div data-carousel='{
        "loadingClasses": "opacity-0",
        "dotsItemClasses": "carousel-box carousel-active:bg-primary",
        "isAutoPlay": false, "speed": 7000
    }' class="relative w-full rounded-xl overflow-hidden shadow-lg">
        <div class="carousel h-1/2 md:h-96">
            <div class="carousel-body h-full opacity-0">
                <div class="carousel-slide active">
                    <div class="relative h-full w-full">
                        <img src="{{ asset('storage/images/slider/slider-m-1.jpg') }}" 
                             alt="Доставка по Батуми - бесплатно!" 
                             class="h-full w-full object-cover md:hidden">
                        <img src="{{ asset('storage/images/slider/slider-d-1.jpg') }}" 
                             alt="Доставка по Батуми - бесплатно!" 
                             class="h-full w-full object-cover hidden md:block">
                        <!-- Градиентная подложка -->
                        <!-- <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-transparent to-black/60"></div> -->
                        <!-- Текстовый блок без размытия -->
                        <!-- <div class="absolute inset-0 flex flex-col items-top justify-start pt-10">
                            <div class="text-left text-white max-w-4xl mx-4 px-6 md:px-20">
                            <p class="text-base sm:text-2xl mb-3">Попробуй в Батуми!</p>    
                            <h2 class="text-2xl font-bold sm:text-3xl uppercase">Авторское меню</h2>
                            <h3 class="mb-4 text-3xl font-black sm:text-5xl">by Nancy Topko</h3>
                            <p class="text-xl sm:text-2xl">Победитель<br> Мастер Шеф<br>Украина 15<br></p> 
                            </div>  
                            
                        </div>
                        <div class="absolute bottom-[100px] md:bottom-[50px] left-0 right-0 flex justify-center">
                            <a href="#menu-tab" 
                               @click.prevent="document.getElementById('menu-tab').click(); setTimeout(() => document.getElementById('menu-content').scrollIntoView({ behavior: 'smooth', block: 'start' }), 100)"
                               type="button" 
                               class="border border-white text-white bg-black/20 backdrop-blur-xs px-6 py-3 rounded-full hover:bg-white/10 transition-colors cursor-pointer">
                                Посмотреть меню
                            </a>                          
                        </div> -->
                    </div>
                </div>
                <div class="carousel-slide">
                    <div class="relative h-full w-full">
                        <img src="{{ asset('storage/images/slider/slider-m-2.jpg') }}" 
                             alt="Скидка 10% от 100 лари" 
                             class="h-full w-full object-cover md:hidden">
                        <img src="{{ asset('storage/images/slider/slider-d-2.jpg') }}" 
                             alt="Скидка 10% от 100 лари" 
                             class="h-full w-full object-cover hidden md:block">

                        <!-- <div class="absolute inset-0 flex flex-col items-start justify-start pt-10 px-6 md:px-20">                        
                            <h2 class="mb-4 text-4xl font-black sm:text-5xl">{{ __('frontend.slider_2_title') }}</h2>
                            <p class="text-sm sm:text-2xl">{{ __('frontend.slider_2_desc') }}</p>
                                <div class="flex flex-col gap-2 mt-4">
                                    <a href="#menu-tab" 
                                        @click.prevent="document.getElementById('menu-tab').click(); setTimeout(() => document.getElementById('menu-content').scrollIntoView({ behavior: 'smooth', block: 'start' }), 100)"
                                        type="button" 
                                        class=" text-white bg-emerald-600 backdrop-blur-xs px-6 py-3 rounded-full hover:bg-white/10 transition-colors cursor-pointer uppercase">
                                        Заказать
                                    </a>         
                                </div>
                        </div>                        -->
                    </div>
                </div>
                <div class="carousel-slide">
                    <div class="relative h-full w-full">
                        <img src="{{ asset('storage/images/slider/slider-m-3.jpg') }}" 
                             alt="Скидка 15% на самовывоз" 
                             class="h-full w-full object-cover">
                        <img src="{{ asset('storage/images/slider/slider-d-3.jpg') }}" 
                             alt="Скидка 15% на самовывоз" 
                             class="h-full w-full object-cover hidden md:block">

                         <!-- <div class="absolute inset-0 flex flex-col items-start justify-start pt-10 px-6 md:px-20">                        
                            <h2 class="mb-4 text-4xl font-black sm:text-5xl">{{ __('frontend.slider_3_title') }}</h2>
                            <p class="text-sm sm:text-2xl">{{ __('frontend.slider_3_desc') }}</p>
                                <div class="flex flex-col gap-2 mt-4">
                                    <a href="#constructor-tab" 
                                        @click.prevent="document.getElementById('constructor-tab').click(); setTimeout(() => document.getElementById('constructor-content').scrollIntoView({ behavior: 'smooth', block: 'start' }), 100)"
                                        type="button" 
                                        class="border border-white text-white bg-emerald-600 backdrop-blur-xs px-6 py-3 rounded-full hover:bg-white/10 transition-colors cursor-pointer">
                                        {{ __('frontend.slider_3_button') }}
                                    </a>         
                                </div>
                        </div>                         -->
                    </div>
                </div>
                <div class="carousel-slide">
                    <div class="relative h-full w-full">
                        <img src="{{ asset('storage/images/slider/slider-4.jpg') }}" 
                             alt="Боулы" 
                             class="h-full w-full object-cover md:hidden">
                        <img src="{{ asset('storage/images/slider/slider-4-desktop.jpg') }}" 
                             alt="Собери сам" 
                             class="h-full w-full object-cover hidden md:block">                        
                            <div class="absolute inset-0 flex flex-col items-center justify-center">  
                                <!-- <h2 class="mb-4 text-4xl text-white font-bold sm:text-5xl text-center">{{ __('frontend.slider_4_title') }}</h2>
                                <p class="text-sm sm:text-2xl text-white">{{ __('frontend.slider_4_desc') }}</p>
                                <div class="flex flex-col gap-2 mt-4">
                                    <a href="#constructor-tab" 
                                        @click.prevent="document.getElementById('constructor-tab').click(); setTimeout(() => document.getElementById('constructor-content').scrollIntoView({ behavior: 'smooth', block: 'start' }), 100)"
                                        type="button" 
                                        class="border border-white text-white bg-black/20 backdrop-blur-xs px-6 py-3 rounded-full hover:bg-white/10 transition-colors cursor-pointer uppercase">
                                        {{ __('frontend.slider_4_button') }}
                                    </a>         
                                </div>                             -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="carousel-prev hidden md:flex start-5 max-sm:start-3 carousel-disabled:opacity-50 size-9.5 bg-base-100 items-center justify-center rounded-full shadow-base-300/20 shadow-sm">
            <span class="icon-[tabler--chevron-left] size-5"></span>
            <span class="sr-only">Previous</span>
        </button>
        <button type="button" class="carousel-next hidden md:flex end-5 max-sm:end-3 carousel-disabled:opacity-50 size-9.5 bg-base-100 items-center justify-center rounded-full shadow-base-300/20 shadow-sm">
            <span class="icon-[tabler--chevron-right] size-5"></span>
            <span class="sr-only">Next</span>
        </button>

        <div class="carousel-pagination absolute bottom-3 end-0 start-0 flex justify-center gap-3"></div>
    </div>

    <!-- Табы -->
    @php
        $tabsTop = $siteOrdersEnabled ? 'top-20' : 'top-[136px]';
        $categoryLinksTop = $siteOrdersEnabled ? 'top-[144px]' : 'top-[210px]';
        $scrollMt = $siteOrdersEnabled ? 'scroll-mt-[210px]' : 'scroll-mt-[270px]';
    @endphp
    <div class="mt-8">
        <div class="sticky {{ $tabsTop }} z-40 -mx-4 px-4 bg-base-100 shadow-sm">
            <nav class="tabs tabs-bordered" aria-label="Tabs" role="tablist" aria-orientation="horizontal">
                <button type="button" 
                        class="tab w-full h-16 text-base md:text-lg active-tab:tab-active active" 
                        id="menu-tab" 
                        data-tab="#menu-content" 
                        aria-controls="menu-content" 
                        role="tab" 
                        aria-selected="true">
                    <span class="icon-[tabler--checkup-list] mr-2 size-6"></span>
                    {{ __('frontend.menu_tab') }}
                </button>
                <button type="button" 
                        class="tab w-full h-16 text-base md:text-lg font-medium active-tab:tab-active" 
                        id="constructor-tab" 
                        data-tab="#constructor-content" 
                        aria-controls="constructor-content" 
                        role="tab" 
                        aria-selected="false">
                    <span class="icon-[tabler--category-plus] mr-2 size-5"></span>
                    {{ __('frontend.constructor_tab') }}
                </button>
                <button type="button"
                        class="tab w-full h-16 text-base md:text-lg font-medium active-tab:tab-active"
                        id="breakfast-constructor-tab"
                        data-tab="#breakfast-constructor-content"
                        aria-controls="breakfast-constructor-content"
                        role="tab"
                        aria-selected="false">
                    <span class="icon-[tabler--coffee] mr-2 size-5"></span>
                    {{ __('frontend.breakfast_constructor_tab') }}
                </button>
            </nav>
        </div>

        <!-- Контент табов -->
        <!-- <div class="rounded-box bg-base-100 p-6 shadow-md"> -->
            <!-- Таб Меню -->
            <div id="menu-content" role="tabpanel" aria-labelledby="menu-tab">
                @if(!$dishCategories->isEmpty())
                    @php
                        $categoryBadgeColors = ['badge-primary', 'badge-success', 'badge-info', 'badge-warning', 'badge-error'];
                    @endphp
                    <nav class="sticky {{ $categoryLinksTop }} z-39 -mx-4 px-4 py-3 mb-6 border-b border-base-content/10 bg-base-100 shadow-sm" aria-label="{{ __('frontend.menu_tab') }}">
                        <div class="menu-category-slider md:hidden overflow-x-auto overscroll-x-contain snap-x snap-mandatory">
                            <div class="flex w-max min-w-full flex-nowrap gap-2">
                                @foreach($dishCategories as $category)
                                    <div class="snap-start shrink-0">
                                        @include('frontend.partials.menu-category-link', [
                                            'category' => $category,
                                            'badgeColor' => $categoryBadgeColors[$loop->index % count($categoryBadgeColors)],
                                        ])
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="hidden md:flex flex-wrap gap-2">
                            @foreach($dishCategories as $category)
                                @include('frontend.partials.menu-category-link', [
                                    'category' => $category,
                                    'badgeColor' => $categoryBadgeColors[$loop->index % count($categoryBadgeColors)],
                                ])
                            @endforeach
                        </div>
                    </nav>
                @endif
                @if($dishCategories->isEmpty())
                    <div class="text-center py-12">
                        <span class="icon-[tabler--shopping-bag-x] size-16 text-base-content/30 mb-4"></span>
                        <p class="text-base-content/60">{{ __('frontend.no_dishes_available') }}</p>
                    </div>
                @else
                    @foreach($dishCategories as $category)
                        <div id="menu-category-{{ $category->id }}" class="mb-10 {{ $scrollMt }}">
                            <h3 class="mb-6 flex items-center gap-2 text-2xl font-bold">
                                <span class="{{ $category->icon_class ?: 'icon-[tabler--bowl-chopsticks]' }} size-6 text-emerald-600"></span>
                                {{ $category->name }}
                            </h3>
                            
                            @if($category->dishes->isEmpty())
                                <p class="text-base-content/50 italic">{{ __('frontend.no_dishes_in_category') }}</p>
                            @else
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach($category->dishes as $dish)
                                        <div class="group relative overflow-hidden rounded-2xl border border-emerald-600/20 bg-base-100 hover:border-emerald-500/50 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                                            <figure class="h-76 overflow-hidden">
                                                @if($dish->image)
                                                    <img src="{{ asset('storage/' . $dish->image) }}" 
                                                         alt="{{ $dish->name }}" 
                                                         class="h-full w-full object-cover rounded-t-2xl transition-transform duration-500 group-hover:scale-105">
                                                @else
                                                    <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop" 
                                                         alt="{{ $dish->name }}" 
                                                         class="h-full w-full object-cover rounded-t-2xl transition-transform duration-500 group-hover:scale-105">
                                                @endif
                                            </figure>
                                            <div class="card-body px-6">
                                                <h4 class="card-title text-lg">{{ $dish->name }}</h4>
                                                
                                                @if($dish->description)
                                                    <p class="text-sm text-base-content/70 line-clamp-4">{{ $dish->description }}</p>
                                                @endif
                                                
                                                <!-- Пищевая ценность блюда -->
                                                @if($dish->calories || $dish->proteins || $dish->fats || $dish->carbohydrates)
                                                    <div class="mt-2">
                                                        <p class="text-xs font-medium text-base-content/60 mb-1">{{ __('frontend.kbgu') }}:</p>
                                                        <div class="flex flex-wrap gap-1 text-xs">
                                                            @if($dish->calories)
                                                                <span class="badge badge-outline border-dashed badge-info badge-sm">
                                                                    <span class="icon-[tabler--flame] mr-1 size-3"></span>
                                                                    {{ $dish->calories }} {{ __('frontend.calories') }}
                                                                </span>
                                                            @endif
                                                            @if($dish->proteins)
                                                                <span class="badge badge-outline border-dashed badge-success badge-sm">{{ __('frontend.proteins') }}: {{ $dish->proteins }}{{ __('frontend.grams') }}</span>
                                                            @endif
                                                            @if($dish->fats)
                                                                <span class="badge badge-outline border-dashed badge-warning badge-sm">{{ __('frontend.fats') }}: {{ $dish->fats }}{{ __('frontend.grams') }}</span>
                                                            @endif
                                                            @if($dish->carbohydrates)
                                                                <span class="badge badge-outline border-dashed badge-error badge-sm">{{ __('frontend.carbs') }}: {{ $dish->carbohydrates }}{{ __('frontend.grams') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Пищевая ценность соуса -->
                                                @if($dish->sauce_name_ru)
                                                    <div class="mt-2 rounded-lg bg-primary/3 p-2">
                                                        <div class="flex items-center gap-1 mb-1">
                                                            <span class="icon-[tabler--bottle] size-4 text-emerald-600"></span>
                                                            <p class="text-base font-medium text-emerald-600">+ {{ $dish->sauce_name }}</p>
                                                            @if($dish->sauce_weight_volume)
                                                                <span class="text-xs">({{ $dish->sauce_weight_volume }})</span>
                                                            @endif
                                                        </div>
                                                        @if($dish->sauce_calories || $dish->sauce_proteins || $dish->sauce_fats || $dish->sauce_carbohydrates)
                                                            <div class="flex flex-wrap gap-1 text-base">
                                                                @if($dish->sauce_calories)
                                                                    <span class="badge badge-soft badge-info">
                                                                        {{ $dish->sauce_calories }} {{ __('frontend.calories') }}
                                                                    </span>
                                                                @endif
                                                                @if($dish->sauce_proteins)
                                                                    <span class="badge badge-soft badge-success">Б: {{ $dish->sauce_proteins }}{{ __('frontend.grams') }}</span>
                                                                @endif
                                                                @if($dish->sauce_fats)
                                                                    <span class="badge badge-soft badge-warning">Ж: {{ $dish->sauce_fats }}{{ __('frontend.grams') }}</span>
                                                                @endif
                                                                @if($dish->sauce_carbohydrates)
                                                                    <span class="badge badge-soft badge-error">У: {{ $dish->sauce_carbohydrates }}{{ __('frontend.grams') }}</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                <!-- Общая КБЖУ -->
                                                @if(($dish->calories || $dish->proteins || $dish->fats || $dish->carbohydrates) && $dish->sauce_name_ru)
                                                    <div class="mt-2 border-t border-base-content/10 pt-2">
                                                        <p class="text-xs font-semibold text-base-content mb-1">Итого с соусом:</p>
                                                        <div class="flex flex-wrap gap-1 text-xs">
                                                            <span class="badge badge-info">
                                                                <span class="icon-[tabler--flame] mr-1 size-3"></span>
                                                                {{ ($dish->calories ?? 0) + ($dish->sauce_calories ?? 0) }} {{ __('frontend.calories') }}
                                                            </span>
                                                            <span class="badge badge-success">Б: {{ number_format(($dish->proteins ?? 0) + ($dish->sauce_proteins ?? 0), 1) }}{{ __('frontend.grams') }}</span>
                                                            <span class="badge badge-warning">Ж: {{ number_format(($dish->fats ?? 0) + ($dish->sauce_fats ?? 0), 1) }}{{ __('frontend.grams') }}</span>
                                                            <span class="badge badge-error">У: {{ number_format(($dish->carbohydrates ?? 0) + ($dish->sauce_carbohydrates ?? 0), 1) }}{{ __('frontend.grams') }}</span>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($dish->weight_volume)
                                                    <p class="text-xs text-base-content/50 mt-2">Вес блюда: {{ $dish->weight_volume }}</p>
                                                @endif
                                                
                                                <div class="card-actions mt-4 items-center justify-between">
                                                    <div class="flex flex-col">
                                                        @if($dish->discount_price)
                                                            <span class="text-xs text-base-content/50 line-through">{{ number_format($dish->price, 2) }} ₾</span>
                                                            <span class="text-xl font-bold text-primary">{{ number_format($dish->discount_price, 2) }} ₾</span>
                                                        @else
                                                            <span class="text-xl font-bold">{{ number_format($dish->price, 2) }} ₾</span>
                                                        @endif
                                                    </div>
                                                    <button type="button"
                                                            class="btn btn-sm bg-emerald-600 hover:bg-emerald-700 text-white border-0 gap-2 {{ !$siteOrdersEnabled ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                            @click="
                                                                const dishPayload = {
                                                                    id: {{ $dish->id }},
                                                                    name: '{{ addslashes($dish->name) }}',
                                                                    basePrice: {{ $dish->discount_price ?? $dish->price }},
                                                                    price: {{ $dish->discount_price ?? $dish->price }},
                                                                    image: '{{ $dish->image }}',
                                                                    weight: '{{ $dish->weight_volume }}',
                                                                    calories: {{ $dish->calories ?? 0 }},
                                                                    proteins: {{ $dish->proteins ?? 0 }},
                                                                    fats: {{ $dish->fats ?? 0 }},
                                                                    carbs: {{ $dish->carbohydrates ?? 0 }},
                                                                    @if($dish->sauce_name_ru)
                                                                    sauce_name: '{{ addslashes($dish->sauce_name) }}',
                                                                    sauce_weight: '{{ $dish->sauce_weight_volume }}',
                                                                    sauce_calories: {{ $dish->sauce_calories ?? 0 }},
                                                                    sauce_proteins: {{ $dish->sauce_proteins ?? 0 }},
                                                                    sauce_fats: {{ $dish->sauce_fats ?? 0 }},
                                                                    sauce_carbs: {{ $dish->sauce_carbohydrates ?? 0 }},
                                                                    @else
                                                                    sauce_name: null,
                                                                    sauce_weight: null,
                                                                    sauce_calories: 0,
                                                                    sauce_proteins: 0,
                                                                    sauce_fats: 0,
                                                                    sauce_carbs: 0,
                                                                    @endif
                                                                    availableAddons: @js($dish->addons->map(fn ($addon) => [
                                                                        'id' => $addon->id,
                                                                        'name' => $addon->name,
                                                                        'price' => (float) ($addon->pivot->price ?? $addon->price),
                                                                    ])->values())
                                                                };
                                                                if (dishPayload.availableAddons.length) {
                                                                    $dispatch('open-dish-addons', dishPayload);
                                                                } else {
                                                                    $store.cart.addDish(dishPayload);
                                                                    $store.cart.openDrawer();
                                                                }
                                                            ">
                                                        <span class="icon-[tabler--shopping-cart-plus] size-4"></span>
                                                        {{ __('frontend.add_to_cart') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endif

                <!-- Раздел с напитками -->
                @if($drinks->isNotEmpty())
                    <div class="mt-12 border-t border-base-content/10 pt-8">
                        <h3 class="mb-6 flex items-center gap-2 text-2xl font-bold">
                            <span class="icon-[tabler--bottle] size-6 text-primary"></span>
                            Напитки
                        </h3>
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($drinks as $drink)
                                <div class="group relative overflow-hidden rounded-2xl border border-base-200 bg-base-100 hover:border-primary/30 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                                    <figure class="h-84 overflow-hidden">
                                        @if($drink->image)
                                            <img src="{{ asset('storage/' . $drink->image) }}" 
                                                 alt="{{ $drink->name }}" 
                                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                        @else
                                            <img src="https://images.unsplash.com/photo-1437418747212-8d9709afab22?w=400&h=300&fit=crop" 
                                                 alt="{{ $drink->name }}" 
                                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                        @endif
                                    </figure>
                                    <div class="card-body">
                                        <h4 class="card-title text-lg">{{ $drink->name }}</h4>
                                        
                                        @if($drink->description)
                                            <p class="text-sm text-base-content/70 line-clamp-2">{{ $drink->description }}</p>
                                        @endif
                                        
                                        <!-- Пищевая ценность напитка -->
                                        @if($drink->calories || $drink->proteins || $drink->fats || $drink->carbohydrates)
                                            <div class="mt-2">
                                                <div class="flex flex-wrap gap-2 text-xs">
                                                    @if($drink->calories)
                                                        <span class="badge badge-outline badge-sm">
                                                            <span class="icon-[tabler--flame] mr-1 size-3"></span>
                                                            {{ $drink->calories }} {{ __('frontend.calories') }}
                                                        </span>
                                                    @endif
                                                    @if($drink->proteins)
                                                        <span class="badge badge-outline badge-sm">{{ __('frontend.proteins') }}: {{ $drink->proteins }}{{ __('frontend.grams') }}</span>
                                                    @endif
                                                    @if($drink->fats)
                                                        <span class="badge badge-outline badge-sm">{{ __('frontend.fats') }}: {{ $drink->fats }}{{ __('frontend.grams') }}</span>
                                                    @endif
                                                    @if($drink->carbohydrates)
                                                        <span class="badge badge-outline badge-sm">{{ __('frontend.carbs') }}: {{ $drink->carbohydrates }}{{ __('frontend.grams') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        @if($drink->volume)
                                            <p class="text-xs text-base-content/50 mt-2">Объем: {{ $drink->volume }}</p>
                                        @endif
                                        
                                        <div class="card-actions mt-4 items-center justify-between">
                                            <div class="flex flex-col">
                                                @if($drink->discount_price)
                                                    <span class="text-xs text-base-content/50 line-through">{{ number_format($drink->price, 2) }} ₾</span>
                                                    <span class="text-xl font-bold text-primary">{{ number_format($drink->discount_price, 2) }} ₾</span>
                                                @else
                                                    <span class="text-xl font-bold">{{ number_format($drink->price, 2) }} ₾</span>
                                                @endif
                                            </div>
                                            <button type="button"
                                                    class="btn btn-primary btn-sm gap-2 {{ !$siteOrdersEnabled ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                    x-data
                                                    @click="
                                                        $store.cart.addDrink({
                                                            id: {{ $drink->id }},
                                                            name: '{{ addslashes($drink->name) }}',
                                                            price: {{ $drink->discount_price ?? $drink->price }},
                                                            image: '{{ $drink->image }}',
                                                            volume: '{{ $drink->volume }}',
                                                            calories: {{ $drink->calories ?? 0 }},
                                                            proteins: {{ $drink->proteins ?? 0 }},
                                                            fats: {{ $drink->fats ?? 0 }},
                                                            carbs: {{ $drink->carbohydrates ?? 0 }}
                                                        });
                                                        $store.cart.openDrawer();
                                                    ">
                                                <span class="icon-[tabler--shopping-cart-plus] size-4"></span>
                                                {{ __('frontend.add_to_cart') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Таб Конструктор -->
            <div id="constructor-content" class="hidden" role="tabpanel" aria-labelledby="constructor-tab">
                @include('frontend.partials.constructor-section', [
                    'categories' => $constructorCategories,
                    'constructorType' => 'bowl',
                    'titleKey' => 'frontend.build_perfect_bowl',
                    'descKey' => 'frontend.build_perfect_bowl_desc',
                    'unavailableKey' => 'frontend.constructor_unavailable',
                    'summaryKey' => 'frontend.your_bowl',
                    'addToCartKey' => 'frontend.add_bowl_to_cart',
                ])
            </div>

            <!-- Таб Конструктор завтраков -->
            <div id="breakfast-constructor-content" class="hidden" role="tabpanel" aria-labelledby="breakfast-constructor-tab">
                @include('frontend.partials.constructor-section', [
                    'categories' => $breakfastCategories,
                    'constructorType' => 'breakfast',
                    'titleKey' => 'frontend.build_perfect_breakfast',
                    'descKey' => 'frontend.build_perfect_breakfast_desc',
                    'unavailableKey' => 'frontend.breakfast_constructor_unavailable',
                    'summaryKey' => 'frontend.your_breakfast',
                    'addToCartKey' => 'frontend.add_breakfast_to_cart',
                ])
            </div>
        <!-- </div> -->
    </div>

    <div x-data="dishAddonsModal()"
         @open-dish-addons.window="open($event.detail)"
         x-show="isOpen"
         x-cloak
         @keydown.esc.prevent="close()"
         class="fixed inset-0 z-[110] flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-zinc-700/75 backdrop-blur-xs" @click="close()"></div>
        <div class="relative w-full max-w-lg rounded-xl bg-base-100 shadow-2xl max-h-[85vh] flex flex-col">
            <div class="flex items-center justify-between border-b border-base-content/10 px-6 py-4">
                <div>
                    <h3 class="text-xl font-bold" x-text="dish?.name"></h3>
                    <p class="text-sm text-base-content/60">{{ __('frontend.choose_addons') }}</p>
                </div>
                <button type="button" @click="close()" class="btn btn-circle btn-ghost btn-sm">
                    <span class="icon-[tabler--x] size-5"></span>
                </button>
            </div>

            <div class="overflow-y-auto p-6 flex-1 space-y-3">
                <template x-for="addon in selectedAddons" :key="addon.id">
                    <div class="flex items-center justify-between gap-3 rounded-lg bg-base-200/60 p-3">
                        <label class="inline-flex items-center gap-2 cursor-pointer flex-1 min-w-0">
                            <input type="checkbox"
                                   class="checkbox"
                                   :checked="addon.quantity > 0"
                                   @change="toggleAddon(addon.id, $event.target.checked)">
                            <span class="font-medium truncate" x-text="addon.name"></span>
                        </label>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-sm font-semibold text-primary" x-text="parseFloat(addon.price).toFixed(2) + ' ₾'"></span>
                            <div class="flex items-center gap-1" x-show="addon.quantity > 0">
                                <button type="button" class="btn btn-circle btn-xs" @click="changeQty(addon.id, -1)">
                                    <span class="icon-[tabler--minus] size-3"></span>
                                </button>
                                <span class="min-w-5 text-center text-sm font-bold" x-text="addon.quantity"></span>
                                <button type="button" class="btn btn-circle btn-xs" @click="changeQty(addon.id, 1)">
                                    <span class="icon-[tabler--plus] size-3"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="border-t border-base-content/10 px-6 py-4 space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-base-content/60">{{ __('frontend.total') }}</span>
                    <span class="text-lg font-bold text-primary" x-text="totalPrice.toFixed(2) + ' ₾'"></span>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <button type="button" class="btn btn-outline flex-1" @click="addWithoutAddons()">
                        {{ __('frontend.without_addons') }}
                    </button>
                    <button type="button" class="btn btn-primary flex-1" @click="confirmAdd()">
                        {{ __('frontend.add_to_cart') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function dishAddonsModal() {
    return {
        isOpen: false,
        dish: null,
        selectedAddons: [],

        open(dish) {
            this.dish = dish;
            this.selectedAddons = (dish.availableAddons || []).map(addon => ({
                ...addon,
                quantity: 0,
            }));
            this.isOpen = true;
        },

        close() {
            this.isOpen = false;
            this.dish = null;
            this.selectedAddons = [];
        },

        toggleAddon(id, checked) {
            const addon = this.selectedAddons.find(item => item.id === id);
            if (!addon) {
                return;
            }
            addon.quantity = checked ? 1 : 0;
        },

        changeQty(id, delta) {
            const addon = this.selectedAddons.find(item => item.id === id);
            if (!addon) {
                return;
            }
            addon.quantity = Math.max(0, addon.quantity + delta);
        },

        get totalPrice() {
            if (!this.dish) {
                return 0;
            }
            const base = parseFloat(this.dish.basePrice ?? this.dish.price);
            const addons = this.selectedAddons.reduce((sum, addon) => sum + (parseFloat(addon.price) * addon.quantity), 0);
            return base + addons;
        },

        addWithoutAddons() {
            this.selectedAddons.forEach(addon => addon.quantity = 0);
            this.confirmAdd();
        },

        confirmAdd() {
            const payload = {
                ...this.dish,
                addons: this.selectedAddons.filter(addon => addon.quantity > 0),
            };
            this.$store.cart.addDish(payload);
            this.close();
            this.$store.cart.openDrawer();
        },
    };
}

function bowlConstructor(type = 'bowl') {
    return {
        type,
        selectedProducts: [],
        modalCategoryId: null,
        
        openCategoryModal(categoryId) {
            this.modalCategoryId = categoryId;
        },
        
        closeModal() {
            this.modalCategoryId = null;
        },
        
        toggleProduct(product) {
            const existing = this.selectedProducts.find(p => p.id === product.id);
            if (!existing) {
                this.selectedProducts.push({ ...product, quantity: 1 });
            } else {
                existing.quantity++;
            }
        },

        decreaseProduct(id) {
            const existing = this.selectedProducts.find(p => p.id === id);
            if (!existing) {
                return;
            }
            if (existing.quantity > 1) {
                existing.quantity--;
            } else {
                this.selectedProducts = this.selectedProducts.filter(p => p.id !== id);
            }
        },

        removeProduct(id) {
            this.selectedProducts = this.selectedProducts.filter(p => p.id !== id);
        },

        isSelected(id) {
            return this.selectedProducts.some(p => p.id === id);
        },

        getProductQuantity(id) {
            const product = this.selectedProducts.find(p => p.id === id);
            return product ? product.quantity : 0;
        },

        getCategoryProducts(categoryId) {
            return this.selectedProducts.filter(p => p.categoryId === categoryId);
        },
        
        clearBowl() {
            this.selectedProducts = [];
        },
        
        addBowlToCart() {
            if (this.selectedProducts.length === 0) {
                return;
            }
            
            this.$store.cart.addBowl(this.selectedProducts, this.type);
            this.clearBowl();
            this.$store.cart.openDrawer();
        },
        
        get totalPrice() {
            return this.selectedProducts.reduce((sum, p) => sum + parseFloat(p.price) * (p.quantity || 1), 0);
        },
        
        get totalNutrition() {
            return {
                calories: this.selectedProducts.reduce((sum, p) => sum + (p.calories || 0) * (p.quantity || 1), 0),
                proteins: this.selectedProducts.reduce((sum, p) => sum + (p.proteins || 0) * (p.quantity || 1), 0).toFixed(1),
                fats: this.selectedProducts.reduce((sum, p) => sum + (p.fats || 0) * (p.quantity || 1), 0).toFixed(1),
                carbs: this.selectedProducts.reduce((sum, p) => sum + (p.carbs || 0) * (p.quantity || 1), 0).toFixed(1)
            };
        }
    }
}
</script>
@endpush