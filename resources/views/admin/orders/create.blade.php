@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost btn-sm mb-2">
                <span class="icon-[tabler--arrow-left] size-4"></span>
                Назад к списку
            </a>
            <h1 class="text-2xl font-bold">{{ $title }}</h1>
        </div>
    </div>

    <form action="{{ route('admin.orders.store') }}" method="POST" x-data="orderForm()">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Основная форма -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Информация о клиенте -->
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Информация о клиенте</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="label">
                                    <span class="label-text">Имя клиента <span class="text-error">*</span></span>
                                </label>
                                <input type="text" name="customer_name" value="{{ old('customer_name') }}" 
                                       class="input input-bordered w-full @error('customer_name') input-error @enderror" 
                                       required>
                                @error('customer_name')
                                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                @enderror
                            </div>

                            <div>
                                <label class="label">
                                    <span class="label-text">Телефон <span class="text-error">*</span></span>
                                </label>
                                <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}" 
                                       class="input input-bordered w-full @error('customer_phone') input-error @enderror" 
                                       required>
                                @error('customer_phone')
                                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                @enderror
                            </div>

                            <div>
                                <label class="label">
                                    <span class="label-text">Email</span>
                                </label>
                                <input type="email" name="customer_email" value="{{ old('customer_email') }}" 
                                       class="input input-bordered w-full @error('customer_email') input-error @enderror">
                                @error('customer_email')
                                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="label">
                                    <span class="label-text">Способ получения <span class="text-error">*</span></span>
                                </label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" 
                                               name="delivery_type" 
                                               value="delivery" 
                                               x-model="deliveryType"
                                               class="radio radio-primary"
                                               {{ old('delivery_type', 'delivery') === 'delivery' ? 'checked' : '' }}>
                                        <span>Доставка</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" 
                                               name="delivery_type" 
                                               value="pickup" 
                                               x-model="deliveryType"
                                               class="radio radio-primary"
                                               {{ old('delivery_type') === 'pickup' ? 'checked' : '' }}>
                                        <span>Самовывоз</span>
                                    </label>
                                </div>
                                @error('delivery_type')
                                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                @enderror
                            </div>

                            <div class="md:col-span-2" x-show="deliveryType === 'delivery'">
                                <label class="label">
                                    <span class="label-text">Адрес доставки</span>
                                </label>
                                <textarea name="delivery_address" rows="2" 
                                          class="textarea textarea-bordered w-full @error('delivery_address') textarea-error @enderror">{{ old('delivery_address') }}</textarea>
                                @error('delivery_address')
                                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="label">
                                    <span class="label-text">Комментарий</span>
                                </label>
                                <textarea name="comment" rows="2" 
                                          class="textarea textarea-bordered w-full @error('comment') textarea-error @enderror">{{ old('comment') }}</textarea>
                                @error('comment')
                                    <label class="label"><span class="label-text-alt text-error">{{ $message }}</span></label>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Позиции заказа -->
                <div class="card">
                    <div class="card-body">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="card-title">Позиции заказа</h2>
                            <div class="join">
                                <button type="button" @click="addDishItem()" class="btn btn-sm btn-primary join-item gap-2">
                                    <span class="icon-[tabler--bowl] size-4"></span>
                                    Блюдо
                                </button>
                                <button type="button" @click="addBowlItem()" class="btn btn-sm btn-primary join-item gap-2">
                                    <span class="icon-[tabler--tools-kitchen-2] size-4"></span>
                                    Боул
                                </button>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(item, index) in items" :key="index">
                                <div class="p-4 rounded-lg bg-base-200/50">
                                    <input type="hidden" :name="'items[' + index + '][type]'" x-model="item.type">
                                    
                                    <!-- Блюдо -->
                                    <div x-show="item.type === 'dish'" class="flex gap-3">
                                        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <div class="md:col-span-2">
                                                <label class="label"><span class="label-text">Блюдо</span></label>
                                                <select :name="'items[' + index + '][dish_id]'" 
                                                        x-model="item.dish_id"
                                                        class="select select-bordered w-full">
                                                    <option value="">Выберите блюдо</option>
                                                    @foreach($dishes as $dish)
                                                        <option value="{{ $dish->id }}">
                                                            {{ $dish->name }} - {{ number_format($dish->discount_price ?? $dish->price, 2) }} ₾
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="label"><span class="label-text">Количество</span></label>
                                                <input type="number" 
                                                       :name="'items[' + index + '][quantity]'" 
                                                       x-model="item.quantity" 
                                                       min="1" 
                                                       class="input input-bordered w-full">
                                            </div>
                                        </div>
                                        <div class="flex items-end">
                                            <button type="button" 
                                                    @click="removeItem(index)" 
                                                    class="btn btn-ghost btn-circle btn-sm text-error">
                                                <span class="icon-[tabler--trash] size-4"></span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Боул -->
                                    <div x-show="item.type === 'bowl'">
                                        <div class="flex items-center justify-between mb-3">
                                            <h3 class="font-medium">Собранный боул</h3>
                                            <button type="button" 
                                                    @click="removeItem(index)" 
                                                    class="btn btn-ghost btn-circle btn-sm text-error">
                                                <span class="icon-[tabler--trash] size-4"></span>
                                            </button>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                            @foreach($constructorCategories as $category)
                                                <div>
                                                    <label class="label"><span class="label-text">{{ $category->name }}</span></label>
                                                    <select class="select select-bordered select-sm w-full"
                                                            @change="toggleProduct(index, $event.target.value)">
                                                        <option value="">Выбрать...</option>
                                                        @foreach($category->products as $product)
                                                            <option value="{{ $product->id }}">
                                                                {{ $product->name }} ({{ number_format($product->price, 2) }} ₾)
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Выбранные продукты -->
                                        <div x-show="item.bowl_products.length > 0" class="mb-3">
                                            <label class="label"><span class="label-text">Выбранные ингредиенты:</span></label>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="(productId, pIndex) in item.bowl_products" :key="pIndex">
                                                    <div class="badge badge-primary gap-2">
                                                        <span x-text="getProductName(productId)"></span>
                                                        <input type="hidden" :name="'items[' + index + '][bowl_products][]'" :value="productId">
                                                        <button type="button" 
                                                                @click="removeProduct(index, pIndex)" 
                                                                class="btn btn-circle btn-ghost btn-xs">
                                                            <span class="icon-[tabler--x] size-3"></span>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="label"><span class="label-text">Количество</span></label>
                                            <input type="number" 
                                                   :name="'items[' + index + '][quantity]'" 
                                                   x-model="item.quantity" 
                                                   min="1" 
                                                   class="input input-bordered w-full">
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <div x-show="items.length === 0" class="text-center py-8 text-base-content/60">
                                Нажмите "Блюдо" или "Боул" для добавления позиций
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Боковая панель -->
            <div class="space-y-6">
                <!-- Статус -->
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Статус заказа</h2>
                        <select name="status" class="select select-bordered w-full" required>
                            <option value="new" {{ old('status', 'new') === 'new' ? 'selected' : '' }}>Новый</option>
                            <option value="unconfirmed" {{ old('status') === 'unconfirmed' ? 'selected' : '' }}>Не подтверждён</option>
                            <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>В работе</option>
                            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Выполнен</option>
                            <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>Отменён</option>
                        </select>
                    </div>
                </div>

                <!-- Кнопки действий -->
                <div class="card">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-full gap-2">
                            <span class="icon-[tabler--check] size-5"></span>
                            Создать заказ
                        </button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-ghost w-full mt-2">
                            Отмена
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
function orderForm() {
    const productsData = @json($constructorCategories->pluck('products')->flatten()->keyBy('id'));
    
    return {
        items: [],
        productsData: productsData,
        deliveryType: '{{ old('delivery_type', 'delivery') }}',

        addDishItem() {
            this.items.push({ 
                type: 'dish', 
                dish_id: '', 
                quantity: 1 
            });
        },

        addBowlItem() {
            this.items.push({ 
                type: 'bowl', 
                bowl_products: [], 
                quantity: 1 
            });
        },

        removeItem(index) {
            this.items.splice(index, 1);
        },

        toggleProduct(itemIndex, productId) {
            if (!productId) return;
            
            const item = this.items[itemIndex];
            if (!item.bowl_products.includes(productId)) {
                item.bowl_products.push(productId);
            }
        },

        removeProduct(itemIndex, productIndex) {
            this.items[itemIndex].bowl_products.splice(productIndex, 1);
        },

        getProductName(productId) {
            const product = this.productsData[productId];
            return product ? product.name : '';
        }
    }
}
</script>
@endpush
