<div x-data="bowlConstructor('{{ $constructorType }}')">
    <div class="mt-8 mb-6 text-center">
        <h3 class="text-2xl font-bold mb-2">{{ __($titleKey) }}</h3>
        <p class="text-base-content/70">{{ __($descKey) }}</p>
    </div>

    @if($categories->isEmpty())
        <div class="text-center py-12">
            <span class="icon-[tabler--tools-kitchen-off] size-16 text-base-content/30 mb-4"></span>
            <p class="text-base-content/60">{{ __($unavailableKey) }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($categories as $category)
                <div @click="openCategoryModal({{ $category->id }})"
                     class="card cursor-pointer transition-all hover:shadow-xl hover:scale-105 min-h-48 border-2 border-dashed"
                     :class="getCategoryProducts({{ $category->id }}).length > 0 ? 'border-primary bg-primary/5' : 'border-base-300 hover:border-primary/50'">
                    <div class="card-body items-center justify-center p-4">
                        <span class="{{ $category->icon_class ?: 'icon-[tabler--tools-kitchen-2] text-primary' }} size-12 mb-2"></span>
                        <h4 class="text-lg font-bold text-center">{{ $category->name }}</h4>

                        <template x-if="getCategoryProducts({{ $category->id }}).length > 0">
                            <div class="mt-3 w-full space-y-2">
                                <template x-for="product in getCategoryProducts({{ $category->id }})" :key="product.id">
                                    <div class="flex items-center gap-2 bg-base-100 rounded-lg p-2 text-sm">
                                        <img :src="product.image || 'https://via.placeholder.com/40'"
                                             :alt="product.name"
                                             class="size-8 rounded object-cover shrink-0">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium truncate text-xs" x-text="product.name"></p>
                                            <p class="text-xs text-primary font-bold" x-text="(product.price * product.quantity).toFixed(2) + ' ₾'"></p>
                                        </div>
                                        <div class="flex items-center gap-1 shrink-0" @click.stop>
                                            <button type="button"
                                                    @click.stop="decreaseProduct(product.id)"
                                                    class="size-6 rounded-full bg-red-500 hover:bg-red-600 active:scale-90 text-white flex items-center justify-center transition-all shadow-sm">
                                                <span class="icon-[tabler--minus] size-2.5"></span>
                                            </button>
                                            <span class="text-xs font-black min-w-4 text-center tabular-nums" x-text="product.quantity"></span>
                                            <button type="button"
                                                    @click.stop="toggleProduct(product)"
                                                    class="size-6 rounded-full bg-emerald-500 hover:bg-emerald-600 active:scale-90 text-white flex items-center justify-center transition-all shadow-sm">
                                                <span class="icon-[tabler--plus] size-2.5"></span>
                                            </button>
                                        </div>
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

        <div class="rounded-box bg-primary/10 p-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xl font-bold">{{ __($summaryKey) }}</h4>
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
                        {{ __($addToCartKey) }}
                    </button>
                </div>
            </div>
        </div>

        @foreach($categories as $category)
        <div x-show="modalCategoryId === {{ $category->id }}"
             x-cloak
             @keydown.esc.prevent="closeModal()"
             class="fixed inset-0 z-[100] flex items-center justify-center p-4">
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
                <div class="flex items-center justify-between border-b border-base-content/10 px-6 py-4">
                    <h3 class="text-2xl font-bold">{{ $category->name }}</h3>
                    <button @click="closeModal()" class="btn btn-circle btn-ghost btn-sm">
                        <span class="icon-[tabler--x] size-5"></span>
                    </button>
                </div>

                <div class="overflow-y-auto p-6 flex-1">
                    @if($category->products->isEmpty())
                        <p class="text-base-content/50 italic text-center py-8">{{ __('frontend.no_products_in_category') }}</p>
                    @else
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                            @foreach($category->products as $product)
                                @php
                                    $variant = $product->variantFor($category->type);
                                @endphp
                                <div @click="toggleProduct({
                                    id: {{ $product->id }},
                                    name: '{{ addslashes($product->name) }}',
                                    price: {{ $variant?->price ?? 0 }},
                                    categoryId: {{ $category->id }},
                                    category: '{{ addslashes($category->name) }}',
                                    image: '{{ $product->image ? asset('storage/' . $product->image) : '' }}',
                                    calories: {{ $variant?->calories ?? 0 }},
                                    proteins: {{ $variant?->proteins ?? 0 }},
                                    fats: {{ $variant?->fats ?? 0 }},
                                    carbs: {{ $variant?->carbohydrates ?? 0 }}
                                })"
                                     class="group relative overflow-hidden rounded-xl bg-base-200 cursor-pointer transition-all duration-300 hover:shadow-lg hover:ring-2 hover:ring-emerald-500/40 hover:-translate-y-0.5"
                                     :class="{ 'ring-2 ring-primary bg-primary/10': isSelected({{ $product->id }}) }">
                                    <figure class="h-32 overflow-hidden relative">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}"
                                                 alt="{{ $product->name }}"
                                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                        @else
                                            <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=200&h=150&fit=crop"
                                                 alt="{{ $product->name }}"
                                                 class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105">
                                        @endif
                                        <div x-show="isSelected({{ $product->id }})"
                                             class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent flex items-end justify-center pb-3">
                                            <div class="flex items-center gap-1.5 bg-white/95 backdrop-blur-sm rounded-full px-1.5 py-1 shadow-2xl border border-white/50"
                                                 @click.stop>
                                                <button type="button"
                                                        @click.stop="decreaseProduct({{ $product->id }})"
                                                        class="size-7 rounded-full bg-red-500 hover:bg-red-600 active:scale-90 text-white flex items-center justify-center transition-all shadow-sm">
                                                    <span class="icon-[tabler--minus] size-3.5"></span>
                                                </button>
                                                <span class="text-sm font-black min-w-5 text-center text-gray-800 tabular-nums"
                                                      x-text="getProductQuantity({{ $product->id }})"></span>
                                                <button type="button"
                                                        @click.stop="toggleProduct({
                                                            id: {{ $product->id }},
                                                            name: '{{ addslashes($product->name) }}',
                                                            price: {{ $variant?->price ?? 0 }},
                                                            categoryId: {{ $category->id }},
                                                            category: '{{ addslashes($category->name) }}',
                                                            image: '{{ $product->image ? asset('storage/' . $product->image) : '' }}',
                                                            calories: {{ $variant?->calories ?? 0 }},
                                                            proteins: {{ $variant?->proteins ?? 0 }},
                                                            fats: {{ $variant?->fats ?? 0 }},
                                                            carbs: {{ $variant?->carbohydrates ?? 0 }}
                                                        })"
                                                        class="size-7 rounded-full bg-emerald-500 hover:bg-emerald-600 active:scale-90 text-white flex items-center justify-center transition-all shadow-sm">
                                                    <span class="icon-[tabler--plus] size-3.5"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </figure>
                                    <div class="card-body p-3">
                                        <h5 class="text-sm font-medium line-clamp-2">{{ $product->name }}</h5>

                                        @if($variant?->weight_volume)
                                            <p class="text-xs text-base-content/50">{{ $variant->weight_volume }}</p>
                                        @endif

                                        @if($variant?->calories || $variant?->proteins || $variant?->fats || $variant?->carbohydrates)
                                            <div class="mt-1 flex flex-wrap gap-1 text-xs">
                                                @if($variant?->calories)
                                                    <span class="badge badge-outline badge-xs">{{ $variant->calories }} {{ __('frontend.calories') }}</span>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="mt-2 flex items-center justify-between">
                                            <span class="text-base font-bold">{{ number_format($variant?->price ?? 0, 2) }} ₾</span>
                                            <span x-show="isSelected({{ $product->id }})"
                                                  class="badge badge-primary badge-sm"
                                                  x-text="'×' + getProductQuantity({{ $product->id }})">
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

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
