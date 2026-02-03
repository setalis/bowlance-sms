@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
            <a href="{{ route('admin.drinks.index') }}" class="btn btn-text btn-sm mb-4">
                <span class="icon-[tabler--arrow-left] size-4"></span>
                Назад к списку
            </a>
        </div>

        <div class="bg-base-100 shadow-base-300/20 w-full space-y-6 rounded-xl p-6 shadow-md lg:p-8">
            <div>
                <h3 class="text-base-content mb-1.5 text-2xl font-semibold">Редактировать напиток</h3>
                <p class="text-base-content/80">Обновите информацию о напитке</p>
            </div>

            <form action="{{ route('admin.drinks.update', $drink) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Основная информация -->
                <div class="space-y-4">
                    <h4 class="text-base-content text-lg font-semibold">Основная информация</h4>
                    
                    <!-- Переводы -->
                    <div class="space-y-4 border-t border-base-content/10 pt-4">
                        <h5 class="text-base-content text-base font-semibold">Переводы</h5>
                        
                        <!-- Русский язык -->
                        <div class="space-y-3 rounded-lg bg-base-200/50 p-4">
                            <div class="flex items-center gap-2">
                                <span class="icon-[tabler--flag] size-5 text-primary"></span>
                                <h6 class="text-base-content font-medium">Русский язык</h6>
                            </div>
                            
                            <div>
                                <label class="label-text" for="name_ru">Название (RU)*</label>
                                <input type="text" 
                                       name="name_ru" 
                                       placeholder="Например: Кока-Кола" 
                                       class="input @error('name_ru') input-error @enderror" 
                                       id="name_ru" 
                                       value="{{ old('name_ru', $drink->name_ru ?? $drink->getRawOriginal('name')) }}" 
                                       required />
                                @error('name_ru')
                                    <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="label-text" for="description_ru">Описание (RU)</label>
                                <textarea name="description_ru" 
                                          placeholder="Подробное описание напитка" 
                                          class="textarea textarea-bordered @error('description_ru') textarea-error @enderror" 
                                          id="description_ru" 
                                          rows="4">{{ old('description_ru', $drink->description_ru ?? $drink->getRawOriginal('description')) }}</textarea>
                                @error('description_ru')
                                    <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Грузинский язык -->
                        <div class="space-y-3 rounded-lg bg-base-200/50 p-4">
                            <div class="flex items-center gap-2">
                                <span class="icon-[tabler--flag] size-5 text-primary"></span>
                                <h6 class="text-base-content font-medium">ქართული ენა (Грузинский язык)</h6>
                            </div>
                            
                            <div>
                                <label class="label-text" for="name_ka">Название (KA)</label>
                                <input type="text" 
                                       name="name_ka" 
                                       placeholder="მაგალითად: კოკა-კოლა" 
                                       class="input @error('name_ka') input-error @enderror" 
                                       id="name_ka" 
                                       value="{{ old('name_ka', $drink->name_ka) }}" />
                                @error('name_ka')
                                    <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="label-text" for="description_ka">Описание (KA)</label>
                                <textarea name="description_ka" 
                                          placeholder="სასმელის დეტალური აღწერა" 
                                          class="textarea textarea-bordered @error('description_ka') textarea-error @enderror" 
                                          id="description_ka" 
                                          rows="4">{{ old('description_ka', $drink->description_ka) }}</textarea>
                                @error('description_ka')
                                    <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="label-text" for="image">Изображение</label>
                        @if($drink->image)
                            <div class="mb-2">
                                <img src="{{ Storage::url($drink->image) }}" 
                                     alt="{{ $drink->name }}" 
                                     class="h-32 w-auto rounded-lg object-cover" />
                            </div>
                        @endif
                        <input type="file" 
                               name="image" 
                               accept="image/*"
                               class="file-input @error('image') file-input-error @enderror" 
                               id="image" />
                        <span class="text-base-content/60 text-xs mt-1 block">Максимальный размер: 2 МБ. Загрузите новое изображение для замены текущего</span>
                        @error('image')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Цены -->
                <div class="space-y-4 border-t border-base-content/10 pt-6">
                    <h4 class="text-base-content text-lg font-semibold">Цены</h4>
                    
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="label-text" for="price">Цена*</label>
                            <input type="number" 
                                   name="price" 
                                   placeholder="0.00" 
                                   class="input @error('price') input-error @enderror" 
                                   id="price" 
                                   value="{{ old('price', $drink->price) }}" 
                                   step="0.01"
                                   min="0"
                                   required />
                            @error('price')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="label-text" for="discount_price">Цена со скидкой</label>
                            <input type="number" 
                                   name="discount_price" 
                                   placeholder="0.00" 
                                   class="input @error('discount_price') input-error @enderror" 
                                   id="discount_price" 
                                   value="{{ old('discount_price', $drink->discount_price) }}" 
                                   step="0.01"
                                   min="0" />
                            <span class="text-base-content/60 text-xs mt-1 block">Оставьте пустым, если скидки нет</span>
                            @error('discount_price')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Пищевая ценность -->
                <div class="space-y-4 border-t border-base-content/10 pt-6">
                    <h4 class="text-base-content text-lg font-semibold">Пищевая ценность</h4>
                    
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="label-text" for="volume">Объем</label>
                            <input type="text" 
                                   name="volume" 
                                   placeholder="Например: 330 мл или 0,5 л" 
                                   class="input @error('volume') input-error @enderror" 
                                   id="volume" 
                                   value="{{ old('volume', $drink->volume) }}" />
                            @error('volume')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="label-text" for="calories">Калории (ккал)</label>
                            <input type="number" 
                                   name="calories" 
                                   placeholder="0" 
                                   class="input @error('calories') input-error @enderror" 
                                   id="calories" 
                                   value="{{ old('calories', $drink->calories) }}" 
                                   min="0" />
                            @error('calories')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                        <div>
                            <label class="label-text" for="proteins">Белки (г)</label>
                            <input type="number" 
                                   name="proteins" 
                                   placeholder="0.00" 
                                   class="input @error('proteins') input-error @enderror" 
                                   id="proteins" 
                                   value="{{ old('proteins', $drink->proteins) }}" 
                                   step="0.01"
                                   min="0" />
                            @error('proteins')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="label-text" for="fats">Жиры (г)</label>
                            <input type="number" 
                                   name="fats" 
                                   placeholder="0.00" 
                                   class="input @error('fats') input-error @enderror" 
                                   id="fats" 
                                   value="{{ old('fats', $drink->fats) }}" 
                                   step="0.01"
                                   min="0" />
                            @error('fats')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="label-text" for="carbohydrates">Углеводы (г)</label>
                            <input type="number" 
                                   name="carbohydrates" 
                                   placeholder="0.00" 
                                   class="input @error('carbohydrates') input-error @enderror" 
                                   id="carbohydrates" 
                                   value="{{ old('carbohydrates', $drink->carbohydrates) }}" 
                                   step="0.01"
                                   min="0" />
                            @error('carbohydrates')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="label-text" for="fiber">Клетчатка (г)</label>
                            <input type="number" 
                                   name="fiber" 
                                   placeholder="0.00" 
                                   class="input @error('fiber') input-error @enderror" 
                                   id="fiber" 
                                   value="{{ old('fiber', $drink->fiber) }}" 
                                   step="0.01"
                                   min="0" />
                            @error('fiber')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Дополнительно -->
                <div class="space-y-4 border-t border-base-content/10 pt-6">
                    <h4 class="text-base-content text-lg font-semibold">Дополнительно</h4>
                    
                    <div>
                        <label class="label-text" for="sort_order">Сортировка</label>
                        <input type="number" 
                               name="sort_order" 
                               placeholder="0" 
                               class="input @error('sort_order') input-error @enderror" 
                               id="sort_order" 
                               value="{{ old('sort_order', $drink->sort_order) }}" 
                               min="0" />
                        @error('sort_order')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn btn-lg btn-primary btn-gradient">
                        <span class="icon-[tabler--check] size-5"></span>
                        Обновить напиток
                    </button>

                    <a href="{{ route('admin.drinks.index') }}" class="btn btn-lg btn-outline">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
