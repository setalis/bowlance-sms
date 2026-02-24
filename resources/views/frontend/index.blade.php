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
                        <img src="{{ asset('storage/images/slider/slider-1-2.jpg') }}" 
                             alt="Боулы" 
                             class="h-full w-full object-cover md:hidden">
                        <img src="{{ asset('storage/images/slider/slider-1-desktop.png') }}" 
                             alt="Боулы" 
                             class="h-full w-full object-cover hidden md:block">
                        <!-- Градиентная подложка -->
                        <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-transparent to-black/60"></div>
                        <!-- Текстовый блок без размытия -->
                        <div class="absolute inset-0 flex flex-col items-top justify-start pt-10">
                            <div class="text-left text-white max-w-4xl mx-4 px-6 md:px-20">
                                <h2 class="mb-4 text-3xl font-black sm:text-3xl uppercase slider-text">Авторское меню</h2>
                                <h3 class="mb-4 text-4xl font-bold sm:text-5xl slider-text-strong">by Nancy Topko</h3>
                                <p class="text-base sm:text-2xl slider-text">Победитель Мастер Шеф Украина 15<br>Попробуй в Батуми!</p>
                            </div>  
                            
                        </div>
                        <div class="absolute bottom-[100px] md:bottom-[50px] left-0 right-0 flex justify-center">
                            <a href="#menu-tab" 
                               @click.prevent="document.getElementById('menu-tab').click(); setTimeout(() => document.getElementById('menu-content').scrollIntoView({ behavior: 'smooth', block: 'start' }), 100)"
                               type="button" 
                               class="border border-white text-white bg-black/20 backdrop-blur-xs px-6 py-3 rounded-full hover:bg-white/10 transition-colors cursor-pointer">
                                Посмотреть меню
                            </a>                          
                        </div>
                    </div>
                </div>
                <div class="carousel-slide">
                    <div class="relative h-full w-full">
                    <img src="{{ asset('storage/images/slider/slider-2-1.jpg') }}" 
                             alt="Боулы" 
                             class="h-full w-full object-cover md:hidden">
                        <img src="{{ asset('storage/images/slider/slider-2-1-desktop.jpg') }}" 
                             alt="Боулы" 
                             class="h-full w-full object-cover hidden md:block">
                        <!-- Градиентная подложка -->
                        <!-- <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-transparent to-black/60"></div> -->
                        <!-- Текстовый блок с размытием -->
                        <div class="absolute inset-0 md:inset-20 flex items-center">
                            <div class="backdrop-blur-sm bg-black/40 px-8 py-6 rounded-2xl border border-white/10 text-center text-white max-w-4xl mx-4">
                                <h2 class="mb-4 text-4xl font-bold sm:text-5xl slider-text-strong">{{ __('frontend.slider_2_title') }}</h2>
                                <p class="text-xl sm:text-2xl slider-text">{{ __('frontend.slider_2_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-slide">
                    <div class="relative h-full w-full">
                        <img src="https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=1200&h=400&fit=crop" 
                             alt="Собери сам" 
                             class="h-full w-full object-cover">
                        <!-- Градиентная подложка -->
                        <div class="absolute inset-0 bg-gradient-to-b from-black/50 via-transparent to-black/60"></div>
                        <!-- Текстовый блок с размытием -->
                        <div class="absolute inset-0 flex items-center justify-center">                            
                            <div class="backdrop-blur-sm bg-black/40 px-8 py-6 rounded-2xl border border-white/10 text-center text-white max-w-4xl mx-4">
                                <h2 class="mb-4 text-4xl font-bold sm:text-5xl slider-text-strong">{{ __('frontend.build_bowl') }}</h2>
                                <p class="text-xl sm:text-2xl slider-text">{{ __('frontend.build_bowl_desc') }}</p>
                                <div class="flex flex-col gap-2 mt-4">
                                    <a href="#constructor-tab" 
                                        @click.prevent="document.getElementById('constructor-tab').click(); setTimeout(() => document.getElementById('constructor-content').scrollIntoView({ behavior: 'smooth', block: 'start' }), 100)"
                                        type="button" 
                                        class="border border-white text-white bg-emerald-600 backdrop-blur-xs px-6 py-3 rounded-full hover:bg-white/10 transition-colors cursor-pointer">
                                        {{ __('frontend.slider_3_button') }}
                                    </a>         
                                </div>
                            </div>
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
    <div class="mt-8">
        <nav class="tabs tabs-bordered" aria-label="Tabs" role="tablist" aria-orientation="horizontal">
            <button type="button" 
                    class="tab w-full h-16 text-base md:text-lg active-tab:tab-active active" 
                    id="menu-tab" 
                    data-tab="#menu-content" 
                    aria-controls="menu-content" 
                    role="tab" 
                    aria-selected="true">
                <span class="icon-[tabler--checkup-list] mr-2 size-6"></span>
                <!-- <img src="{{ asset('storage/images/menu-icon.png') }}" alt="Меню" class="size-10 mr-2 md:mr-4"> -->
                <!-- <svg xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" class="size-6 mr-2 md:mr-4">
                    <path d="m19.5,13h-11c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h11c.276,0,.5.224.5.5s-.224.5-.5.5Zm-7-4h7c.276,0,.5-.224.5-.5s-.224-.5-.5-.5h-7c-.276,0-.5.224-.5.5s.224.5.5.5Zm-4,7c-.276,0-.5.224-.5.5s.224.5.5.5h4c.276,0,.5-.224.5-.5s-.224-.5-.5-.5h-4Zm-4-6c-1.378,0-2.5-1.121-2.5-2.5v-2.55c-1.14-.232-2-1.243-2-2.45C0,1.121,1.122,0,2.5,0c.618,0,1.21.232,1.664.639,1.048-.82,2.624-.82,3.672,0,.454-.406,1.046-.639,1.664-.639,1.378,0,2.5,1.121,2.5,2.5,0,1.207-.86,2.218-2,2.45v2.55c0,1.379-1.122,2.5-2.5,2.5h-3Zm0-1h3c.827,0,1.5-.673,1.5-1.5v-3c0-.276.224-.5.5-.5.827,0,1.5-.673,1.5-1.5s-.673-1.5-1.5-1.5c-.485,0-.943.239-1.225.64-.088.125-.229.203-.381.211-.157.016-.301-.054-.402-.168-.778-.881-2.205-.881-2.983,0-.101.115-.245.189-.402.168-.153-.008-.293-.086-.381-.211-.282-.4-.74-.64-1.225-.64-.827,0-1.5.673-1.5,1.5s.673,1.5,1.5,1.5c.276,0,.5.224.5.5v3c0,.827.673,1.5,1.5,1.5Zm19.5-4.5v9.515c0,1.736-.677,3.369-1.904,4.597l-3.484,3.484c-1.228,1.228-2.86,1.904-4.597,1.904h-5.515c-2.481,0-4.5-2.019-4.5-4.5v-7c0-.276.224-.5.5-.5s.5.224.5.5v7c0,1.93,1.57,3.5,3.5,3.5h5.515c.335,0,.663-.038.985-.096v-5.404c0-1.379,1.121-2.5,2.5-2.5h5.404c.058-.323.096-.651.096-.985V4.5c0-1.93-1.57-3.5-3.5-3.5h-5c-.276,0-.5-.224-.5-.5s.224-.5.5-.5h5c2.481,0,4.5,2.019,4.5,4.5Zm-1.38,11.5h-5.12c-.827,0-1.5.673-1.5,1.5v5.121c.704-.273,1.354-.682,1.904-1.232l3.484-3.484c.55-.55.959-1.2,1.232-1.904Z"/>
                </svg> -->
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
                <!-- <img src="{{ asset('storage/images/constructor-icon.png') }}" alt="Конструктор" class="size-10 mr-2 md:mr-4"> -->
                {{ __('frontend.constructor_tab') }}
            </button>
        </nav>

        <!-- Контент табов -->
        <!-- <div class="rounded-box bg-base-100 p-6 shadow-md"> -->
            <!-- Таб Меню -->
            <div id="menu-content" role="tabpanel" aria-labelledby="menu-tab" class="pt-6">
                @if(!$dishCategories->isEmpty())
                    @php
                        $categoryBadgeColors = ['badge-primary', 'badge-success', 'badge-info', 'badge-warning', 'badge-error'];
                    @endphp
                    <nav class="flex flex-wrap gap-2 mb-6 pb-4 border-b border-gray-200" aria-label="{{ __('frontend.menu_tab') }}">
                        @foreach($dishCategories as $category)
                            <a href="#menu-category-{{ $category->id }}"
                               @click.prevent="const el = document.querySelector($event.currentTarget.getAttribute('href')); if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })"
                               class="badge badge-soft {{ $categoryBadgeColors[$loop->index % count($categoryBadgeColors)] }} badge-lg gap-1.5 hover:opacity-90 transition-opacity cursor-pointer no-underline">
                                <span class="{{ $category->icon_class ?: 'icon-[tabler--bowl-chopsticks]' }} size-4"></span>
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </nav>
                @endif
                @if($dishCategories->isEmpty())
                    <div class="text-center py-12">
                        <span class="icon-[tabler--shopping-bag-x] size-16 text-base-content/30 mb-4"></span>
                        <p class="text-base-content/60">{{ __('frontend.no_dishes_available') }}</p>
                    </div>
                @else
                    @foreach($dishCategories as $category)
                        <div id="menu-category-{{ $category->id }}" class="mb-10 scroll-mt-24">
                            <h3 class="mb-6 flex items-center gap-2 text-2xl font-bold">
                                <span class="{{ $category->icon_class ?: 'icon-[tabler--bowl-chopsticks]' }} size-6 text-emerald-600"></span>
                                {{ $category->name }}
                            </h3>
                            
                            @if($category->dishes->isEmpty())
                                <p class="text-base-content/50 italic">{{ __('frontend.no_dishes_in_category') }}</p>
                            @else
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach($category->dishes as $dish)
                                        <div class="border border-emerald-600/20 rounded-2xl hover:shadow-md transition-shadow">
                                            <figure class="h-76 overflow-hidden">
                                                @if($dish->image)
                                                    <img src="{{ asset('storage/' . $dish->image) }}" 
                                                         alt="{{ $dish->name }}" 
                                                         class="h-full w-full object-cover rounded-t-2xl">
                                                @else
                                                    <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop" 
                                                         alt="{{ $dish->name }}" 
                                                         class="h-full w-full object-cover rounded-t-2xl">
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
                                                            x-data
                                                            @click="
                                                                $store.cart.addDish({
                                                                    id: {{ $dish->id }},
                                                                    name: '{{ addslashes($dish->name) }}',
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
                                                                    sauce_carbs: {{ $dish->sauce_carbohydrates ?? 0 }}
                                                                    @else
                                                                    sauce_name: null,
                                                                    sauce_weight: null,
                                                                    sauce_calories: 0,
                                                                    sauce_proteins: 0,
                                                                    sauce_fats: 0,
                                                                    sauce_carbs: 0
                                                                    @endif
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
                                <div class="card hover:shadow-xl transition-shadow">
                                    <figure class="h-84 overflow-hidden">
                                        @if($drink->image)
                                            <img src="{{ asset('storage/' . $drink->image) }}" 
                                                 alt="{{ $drink->name }}" 
                                                 class="h-full w-full object-cover">
                                        @else
                                            <img src="https://images.unsplash.com/photo-1437418747212-8d9709afab22?w=400&h=300&fit=crop" 
                                                 alt="{{ $drink->name }}" 
                                                 class="h-full w-full object-cover">
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
                <div x-data="bowlConstructor()">
                    <div class="mt-8 mb-6 text-center">
                        <h3 class="text-2xl font-bold mb-2">{{ __('frontend.build_perfect_bowl') }}</h3>
                        <p class="text-base-content/70">{{ __('frontend.build_perfect_bowl_desc') }}</p>
                    </div>

                    @if($constructorCategories->isEmpty())
                        <div class="text-center py-12">
                            <span class="icon-[tabler--tools-kitchen-off] size-16 text-base-content/30 mb-4"></span>
                            <p class="text-base-content/60">{{ __('frontend.constructor_unavailable') }}</p>
                        </div>
                    @else
                        <!-- Зоны категорий -->
                        <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($constructorCategories as $category)
                                <div @click="openCategoryModal({{ $category->id }})" 
                                     class="card cursor-pointer transition-all hover:shadow-xl hover:scale-105 min-h-48 border-2 border-dashed"
                                     :class="getCategoryProducts({{ $category->id }}).length > 0 ? 'border-primary bg-primary/5' : 'border-base-300 hover:border-primary/50'">
                                    <div class="card-body items-center justify-center p-4">
                                        <span class="{{ $category->icon_class ?: 'icon-[tabler--tools-kitchen-2] text-primary' }} size-12 mb-2"></span>
                                        <h4 class="text-lg font-bold text-center">{{ $category->name }}</h4>
                                        
                                        <!-- Выбранные продукты в категории -->
                                        <template x-if="getCategoryProducts({{ $category->id }}).length > 0">
                                            <div class="mt-3 w-full space-y-2">
                                                <template x-for="product in getCategoryProducts({{ $category->id }})" :key="product.id">
                                                    <div class="flex items-center gap-2 bg-base-100 rounded-lg p-2 text-sm">
                                                        <img :src="product.image || 'https://via.placeholder.com/40'" 
                                                             :alt="product.name" 
                                                             class="size-8 rounded object-cover">
                                                        <div class="flex-1 min-w-0">
                                                            <p class="font-medium truncate text-xs" x-text="product.name"></p>
                                                            <p class="text-xs text-primary font-bold" x-text="product.price + ' ₾'"></p>
                                                        </div>
                                                        <button type="button" 
                                                                @click.stop="removeProduct(product.id)" 
                                                                class="btn btn-ghost btn-circle btn-xs">
                                                            <span class="icon-[tabler--x] size-3"></span>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                        
                                        <template x-if="getCategoryProducts({{ $category->id }}).length === 0">
                                            <p class="text-sm text-base-content/50 text-center mt-2">{{ __('frontend.click_to_select') }}</p>
                                        </template>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Итоговая информация -->
                        <div class="rounded-box bg-primary/10 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-xl font-bold">{{ __('frontend.your_bowl') }}</h4>
                                <button type="button" 
                                        @click="clearBowl()" 
                                        class="btn btn-ghost btn-sm gap-2"
                                        x-show="selectedProducts.length > 0">
                                    <span class="icon-[tabler--trash] size-4"></span>
                                    {{ __('frontend.clear') }}
                                </button>
                            </div>
                            
                            <div x-show="selectedProducts.length === 0" class="text-center py-4 text-base-content/50">
                                {{ __('frontend.select_products') }}
                            </div>
                            
                            <div x-show="selectedProducts.length > 0">
                                <!-- Итоговая информация -->
                                <div class="border-t border-base-content/10 pt-4">
                                    <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">
                                        <div class="text-center">
                                            <p class="text-xs text-base-content/50">{{ __('frontend.nutrition_calories') }}</p>
                                            <p class="text-lg font-bold" x-text="totalNutrition.calories + ' {{ __('frontend.calories') }}'"></p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs text-base-content/50">{{ __('frontend.nutrition_proteins') }}</p>
                                            <p class="text-lg font-bold" x-text="totalNutrition.proteins + ' {{ __('frontend.grams') }}'"></p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs text-base-content/50">{{ __('frontend.nutrition_fats') }}</p>
                                            <p class="text-lg font-bold" x-text="totalNutrition.fats + ' {{ __('frontend.grams') }}'"></p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs text-base-content/50">{{ __('frontend.nutrition_carbs') }}</p>
                                            <p class="text-lg font-bold" x-text="totalNutrition.carbs + ' {{ __('frontend.grams') }}'"></p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs text-base-content/50">{{ __('frontend.total') }}</p>
                                            <p class="text-2xl font-bold text-primary" x-text="totalPrice.toFixed(2) + ' ₾'"></p>
                                        </div>
                                    </div>
                                    
                                    <button type="button"
                                            class="btn btn-primary w-full mt-4 gap-2 {{ !$siteOrdersEnabled ? 'opacity-50 cursor-not-allowed' : '' }}"
                                            :disabled="selectedProducts.length === 0"
                                            @click="addBowlToCart()">
                                        <span class="icon-[tabler--shopping-cart-plus] size-5"></span>
                                        {{ __('frontend.add_bowl_to_cart') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Модальные окна для каждой категории -->
                        @foreach($constructorCategories as $category)
                        <div x-show="modalCategoryId === {{ $category->id }}"
                             x-cloak
                             @keydown.esc.prevent="closeModal()"
                             class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                            <!-- Backdrop -->
                            <div
                                x-show="modalCategoryId === {{ $category->id }}"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0"
                                @click="closeModal()"
                                class="fixed inset-0 bg-zinc-700/75 backdrop-blur-xs"
                            ></div>
                            
                            <!-- Modal Content -->
                            <div
                                x-show="modalCategoryId === {{ $category->id }}"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="relative w-full max-w-4xl rounded-xl bg-base-100 shadow-2xl max-h-[85vh] flex flex-col"
                            >
                                <!-- Header -->
                                <div class="flex items-center justify-between border-b border-base-content/10 px-6 py-4">
                                    <h3 class="text-2xl font-bold">{{ $category->name }}</h3>
                                    <button @click="closeModal()" class="btn btn-circle btn-ghost btn-sm">
                                        <span class="icon-[tabler--x] size-5"></span>
                                    </button>
                                </div>
                                
                                <!-- Body -->
                                <div class="overflow-y-auto p-6 flex-1">

                                        @if($category->products->isEmpty())
                                            <p class="text-base-content/50 italic text-center py-8">{{ __('frontend.no_products_in_category') }}</p>
                                        @else
                                            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                                                @foreach($category->products as $product)
                                                    <div @click="toggleProduct({
                                                        id: {{ $product->id }},
                                                        name: '{{ addslashes($product->name) }}',
                                                        price: {{ $product->price }},
                                                        categoryId: {{ $category->id }},
                                                        category: '{{ addslashes($category->name) }}',
                                                        image: '{{ $product->image ? asset('storage/' . $product->image) : '' }}',
                                                        calories: {{ $product->calories ?? 0 }},
                                                        proteins: {{ $product->proteins ?? 0 }},
                                                        fats: {{ $product->fats ?? 0 }},
                                                        carbs: {{ $product->carbohydrates ?? 0 }}
                                                    })"
                                                         class="card bg-base-200 cursor-pointer transition-all hover:shadow-lg hover:bg-base-300"
                                                         :class="{ 'ring-2 ring-primary bg-primary/10': isSelected({{ $product->id }}) }">
                                                        <figure class="h-32 overflow-hidden relative">
                                                            @if($product->image)
                                                                <img src="{{ asset('storage/' . $product->image) }}" 
                                                                     alt="{{ $product->name }}" 
                                                                     class="h-full w-full object-cover">
                                                            @else
                                                                <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=200&h=150&fit=crop" 
                                                                     alt="{{ $product->name }}" 
                                                                     class="h-full w-full object-cover">
                                                            @endif
                                                            <div x-show="isSelected({{ $product->id }})" 
                                                                 class="absolute inset-0 bg-primary/30 flex items-center justify-center">
                                                                <span class="icon-[tabler--check] size-10 text-white bg-primary rounded-full p-1"></span>
                                                            </div>
                                                        </figure>
                                                        <div class="card-body p-3">
                                                            <h5 class="text-sm font-medium line-clamp-2">{{ $product->name }}</h5>
                                                            
                                                            @if($product->weight_volume)
                                                                <p class="text-xs text-base-content/50">{{ $product->weight_volume }}</p>
                                                            @endif
                                                            
                                                            <!-- Пищевая ценность -->
                                                            @if($product->calories || $product->proteins || $product->fats || $product->carbohydrates)
                                                                <div class="mt-1 flex flex-wrap gap-1 text-xs">
                                                                    @if($product->calories)
                                                                        <span class="badge badge-outline badge-xs">{{ $product->calories }} {{ __('frontend.calories') }}</span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                            
                                                            <div class="mt-2 flex items-center justify-between">
                                                                <span class="text-base font-bold">{{ number_format($product->price, 2) }} ₾</span>
                                                                <span x-show="isSelected({{ $product->id }})" 
                                                                      class="badge badge-primary badge-sm">
                                                                    {{ __('frontend.selected') }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Footer -->
                                    @if(!$category->products->isEmpty())
                                    <div class="border-t border-base-content/10 px-6 py-4 flex justify-end gap-2">
                                        <button @click="closeModal()" class="btn btn-primary btn-lg gap-2">
                                            <span class="icon-[tabler--check] size-5"></span>
                                            {{ __('frontend.done') }}
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        <!-- </div> -->
    </div>
@endsection

@push('scripts')
<script>
function bowlConstructor() {
    return {
        selectedProducts: [],
        modalCategoryId: null,
        
        openCategoryModal(categoryId) {
            this.modalCategoryId = categoryId;
        },
        
        closeModal() {
            this.modalCategoryId = null;
        },
        
        toggleProduct(product) {
            const index = this.selectedProducts.findIndex(p => p.id === product.id);
            if (index === -1) {
                this.selectedProducts.push(product);
            } else {
                this.selectedProducts.splice(index, 1);
            }
        },
        
        removeProduct(id) {
            this.selectedProducts = this.selectedProducts.filter(p => p.id !== id);
        },
        
        isSelected(id) {
            return this.selectedProducts.some(p => p.id === id);
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
            
            // Добавляем боул в корзину через Alpine store
            this.$store.cart.addBowl(this.selectedProducts);
            
            // Очищаем выбранные продукты
            this.clearBowl();
            
            // Открываем drawer корзины
            this.$store.cart.openDrawer();
        },
        
        get totalPrice() {
            return this.selectedProducts.reduce((sum, p) => sum + parseFloat(p.price), 0);
        },
        
        get totalNutrition() {
            return {
                calories: this.selectedProducts.reduce((sum, p) => sum + (p.calories || 0), 0),
                proteins: this.selectedProducts.reduce((sum, p) => sum + (p.proteins || 0), 0).toFixed(1),
                fats: this.selectedProducts.reduce((sum, p) => sum + (p.fats || 0), 0).toFixed(1),
                carbs: this.selectedProducts.reduce((sum, p) => sum + (p.carbs || 0), 0).toFixed(1)
            };
        }
    }
}
</script>
@endpush