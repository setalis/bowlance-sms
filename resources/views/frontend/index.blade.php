@extends('layouts.front.app')

@section('content')
    <!-- Слайдер -->
    <div data-carousel='{
        "loadingClasses": "opacity-0",
        "dotsItemClasses": "carousel-box carousel-active:bg-primary",
        "isAutoPlay": true, "speed": 5000
    }' class="relative w-full rounded-xl overflow-hidden shadow-lg">
        <div class="carousel h-96">
            <div class="carousel-body h-full opacity-0">
                <div class="carousel-slide active">
                    <div class="relative h-full w-full">
                        <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=1200&h=400&fit=crop" 
                             alt="Боулы" 
                             class="h-full w-full object-cover">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                            <div class="text-center text-white">
                                <h2 class="mb-4 text-4xl font-bold sm:text-5xl">Bowlance</h2>
                                <p class="text-xl sm:text-2xl">{{ __('frontend.tagline') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-slide">
                    <div class="relative h-full w-full">
                        <img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=1200&h=400&fit=crop" 
                             alt="Свежие продукты" 
                             class="h-full w-full object-cover">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                            <div class="text-center text-white">
                                <h2 class="mb-4 text-4xl font-bold sm:text-5xl">{{ __('frontend.fresh_products') }}</h2>
                                <p class="text-xl sm:text-2xl">{{ __('frontend.fresh_products_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-slide">
                    <div class="relative h-full w-full">
                        <img src="https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=1200&h=400&fit=crop" 
                             alt="Собери сам" 
                             class="h-full w-full object-cover">
                        <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                            <div class="text-center text-white">
                                <h2 class="mb-4 text-4xl font-bold sm:text-5xl">{{ __('frontend.build_bowl') }}</h2>
                                <p class="text-xl sm:text-2xl">{{ __('frontend.build_bowl_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="carousel-prev start-5 max-sm:start-3 carousel-disabled:opacity-50 size-9.5 bg-base-100 flex items-center justify-center rounded-full shadow-base-300/20 shadow-sm">
            <span class="icon-[tabler--chevron-left] size-5"></span>
            <span class="sr-only">Previous</span>
        </button>
        <button type="button" class="carousel-next end-5 max-sm:end-3 carousel-disabled:opacity-50 size-9.5 bg-base-100 flex items-center justify-center rounded-full shadow-base-300/20 shadow-sm">
            <span class="icon-[tabler--chevron-right] size-5"></span>
            <span class="sr-only">Next</span>
        </button>

        <div class="carousel-pagination absolute bottom-3 end-0 start-0 flex justify-center gap-3"></div>
    </div>

    <!-- Табы -->
    <div class="mt-8">
        <nav class="tabs tabs-lifted" aria-label="Tabs" role="tablist" aria-orientation="horizontal">
            <button type="button" 
                    class="tab h-14 text-lg active-tab:tab-active active" 
                    id="menu-tab" 
                    data-tab="#menu-content" 
                    aria-controls="menu-content" 
                    role="tab" 
                    aria-selected="true">
                <span class="icon-[tabler--menu-2] mr-2 size-5"></span>
                {{ __('frontend.menu_tab') }}
            </button>
            <button type="button" 
                    class="tab h-14 text-lg active-tab:tab-active" 
                    id="constructor-tab" 
                    data-tab="#constructor-content" 
                    aria-controls="constructor-content" 
                    role="tab" 
                    aria-selected="false">
                <span class="icon-[tabler--tools-kitchen-2] mr-2 size-5"></span>
                {{ __('frontend.constructor_tab') }}
            </button>
        </nav>

        <!-- Контент табов -->
        <div class="rounded-box bg-base-100 p-6 shadow-md">
            <!-- Таб Меню -->
            <div id="menu-content" role="tabpanel" aria-labelledby="menu-tab">
                @if($dishCategories->isEmpty())
                    <div class="text-center py-12">
                        <span class="icon-[tabler--shopping-bag-x] size-16 text-base-content/30 mb-4"></span>
                        <p class="text-base-content/60">{{ __('frontend.no_dishes_available') }}</p>
                    </div>
                @else
                    @foreach($dishCategories as $category)
                        <div class="mb-10">
                            <h3 class="mb-6 flex items-center gap-2 text-2xl font-bold">
                                <span class="icon-[tabler--category] size-6 text-primary"></span>
                                {{ $category->name }}
                            </h3>
                            
                            @if($category->dishes->isEmpty())
                                <p class="text-base-content/50 italic">{{ __('frontend.no_dishes_in_category') }}</p>
                            @else
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach($category->dishes as $dish)
                                        <div class="card hover:shadow-xl transition-shadow">
                                            <figure class="h-48 overflow-hidden">
                                                @if($dish->image)
                                                    <img src="{{ asset('storage/' . $dish->image) }}" 
                                                         alt="{{ $dish->name }}" 
                                                         class="h-full w-full object-cover">
                                                @else
                                                    <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400&h=300&fit=crop" 
                                                         alt="{{ $dish->name }}" 
                                                         class="h-full w-full object-cover">
                                                @endif
                                            </figure>
                                            <div class="card-body">
                                                <h4 class="card-title text-lg">{{ $dish->name }}</h4>
                                                
                                                @if($dish->description)
                                                    <p class="text-sm text-base-content/70 line-clamp-2">{{ $dish->description }}</p>
                                                @endif
                                                
                                                <!-- Пищевая ценность -->
                                                @if($dish->calories || $dish->proteins || $dish->fats || $dish->carbohydrates)
                                                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                                        @if($dish->calories)
                                                            <span class="badge badge-outline badge-sm">
                                                                <span class="icon-[tabler--flame] mr-1 size-3"></span>
                                                                {{ $dish->calories }} {{ __('frontend.calories') }}
                                                            </span>
                                                        @endif
                                                        @if($dish->proteins)
                                                            <span class="badge badge-outline badge-sm">{{ __('frontend.proteins') }}: {{ $dish->proteins }}{{ __('frontend.grams') }}</span>
                                                        @endif
                                                        @if($dish->fats)
                                                            <span class="badge badge-outline badge-sm">{{ __('frontend.fats') }}: {{ $dish->fats }}{{ __('frontend.grams') }}</span>
                                                        @endif
                                                        @if($dish->carbohydrates)
                                                            <span class="badge badge-outline badge-sm">{{ __('frontend.carbs') }}: {{ $dish->carbohydrates }}{{ __('frontend.grams') }}</span>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if($dish->weight_volume)
                                                    <p class="text-xs text-base-content/50 mt-1">{{ $dish->weight_volume }}</p>
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
                                                            class="btn btn-primary btn-sm gap-2"
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
                                                                    carbs: {{ $dish->carbohydrates ?? 0 }}
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
            </div>

            <!-- Таб Конструктор -->
            <div id="constructor-content" class="hidden" role="tabpanel" aria-labelledby="constructor-tab">
                <div x-data="bowlConstructor()">
                    <div class="mb-6 text-center">
                        <h3 class="text-3xl font-bold mb-2">{{ __('frontend.build_perfect_bowl') }}</h3>
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
                                        <span class="icon-[tabler--tools-kitchen-2] size-12 text-primary mb-2"></span>
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
                                            class="btn btn-primary w-full mt-4 gap-2"
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
        </div>
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
