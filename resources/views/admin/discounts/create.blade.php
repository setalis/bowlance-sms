@extends('layouts.app')

@section('content')
    <div class="space-y-6" x-data="{ scope: '{{ old('scope', 'pickup') }}' }">
        <div>
            <a href="{{ route('admin.discounts.index') }}" class="btn btn-text btn-sm mb-4">
                <span class="icon-[tabler--arrow-left] size-4"></span>
                Назад к списку
            </a>
        </div>

        <div class="bg-base-100 shadow-base-300/20 w-full space-y-6 rounded-xl p-6 shadow-md lg:p-8">
            <div>
                <h3 class="text-base-content mb-1.5 text-2xl font-semibold">Создать скидку</h3>
                <p class="text-base-content/80">Добавьте скидку за самовывоз или по сумме корзины при доставке</p>
            </div>

            <form action="{{ route('admin.discounts.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="label-text" for="name">Название (необязательно)</label>
                        <input type="text"
                               name="name"
                               placeholder="Например: Скидка за самовывоз"
                               class="input @error('name') input-error @enderror"
                               id="name"
                               value="{{ old('name') }}" />
                        @error('name')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="label-text" for="scope">Тип применения <span class="text-error">*</span></label>
                        <select name="scope"
                                id="scope"
                                x-model="scope"
                                class="select select-bordered w-full @error('scope') select-error @enderror"
                                required>
                            @foreach (\App\Enums\DiscountScope::cases() as $discountScope)
                                <option value="{{ $discountScope->value }}" @selected(old('scope', 'pickup') === $discountScope->value)>
                                    {{ $discountScope->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('scope')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div x-show="scope === 'cart_total'" x-cloak>
                        <label class="label-text" for="min_cart_total">Минимальная сумма корзины (₾) <span class="text-error">*</span></label>
                        <input type="number"
                               name="min_cart_total"
                               step="0.01"
                               min="0.01"
                               placeholder="100"
                               class="input @error('min_cart_total') input-error @enderror"
                               id="min_cart_total"
                               value="{{ old('min_cart_total') }}" />
                        <span class="text-base-content/60 text-xs mt-1 block">Скидка применится при доставке, если сумма корзины не ниже указанной</span>
                        @error('min_cart_total')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="label-text" for="size">Размер <span class="text-error">*</span></label>
                            <input type="number"
                                   name="size"
                                   step="0.01"
                                   min="0"
                                   placeholder="{{ old('type') === 'percent' ? '10' : '2.50' }}"
                                   class="input @error('size') input-error @enderror"
                                   id="size"
                                   value="{{ old('size') }}"
                                   required />
                            <span class="text-base-content/60 text-xs mt-1 block">Число: для процента — от 0 до 100, для суммы — в ₾</span>
                            @error('size')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="label-text" for="type">Тип скидки <span class="text-error">*</span></label>
                            <select name="type"
                                    id="type"
                                    class="select select-bordered w-full @error('type') select-error @enderror"
                                    required>
                                <option value="percent" {{ old('type', 'percent') === 'percent' ? 'selected' : '' }}>Процент</option>
                                <option value="amount" {{ old('type') === 'amount' ? 'selected' : '' }}>Сумма (₾)</option>
                            </select>
                            @error('type')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="label cursor-pointer justify-start gap-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox"
                               name="is_active"
                               class="checkbox"
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }} />
                        <span class="label-text">Активна</span>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn btn-lg btn-primary btn-gradient">
                        <span class="icon-[tabler--check] size-5"></span>
                        Создать скидку
                    </button>
                    <a href="{{ route('admin.discounts.index') }}" class="btn btn-lg btn-outline">Отмена</a>
                </div>
            </form>
        </div>
    </div>
@endsection
