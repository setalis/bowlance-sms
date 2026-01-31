@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
            <a href="{{ route('admin.dishes.index') }}" class="btn btn-text btn-sm mb-4">
                <span class="icon-[tabler--arrow-left] size-4"></span>
                Назад к списку
            </a>
        </div>

        <div class="bg-base-100 shadow-base-300/20 w-full space-y-6 rounded-xl p-6 shadow-md lg:p-8">
            <div>
                <h3 class="text-base-content mb-1.5 text-2xl font-semibold">Создать блюдо</h3>
                <p class="text-base-content/80">Добавьте новое блюдо в меню</p>
            </div>

            <form action="{{ route('admin.dishes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Основная информация -->
                <div class="space-y-4">
                    <h4 class="text-base-content text-lg font-semibold">Основная информация</h4>
                    
                    <div>
                        <label class="label-text" for="dish_category_id">Категория*</label>
                        <select name="dish_category_id" 
                                class="select @error('dish_category_id') select-error @enderror" 
                                id="dish_category_id" 
                                required>
                            <option value="">Выберите категорию</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('dish_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('dish_category_id')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

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
                                       placeholder="Например: Борщ украинский" 
                                       class="input @error('name_ru') input-error @enderror" 
                                       id="name_ru" 
                                       value="{{ old('name_ru') }}" 
                                       required />
                                @error('name_ru')
                                    <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="label-text" for="description_ru">Описание (RU)</label>
                                <textarea name="description_ru" 
                                          placeholder="Подробное описание блюда" 
                                          class="textarea textarea-bordered @error('description_ru') textarea-error @enderror" 
                                          id="description_ru" 
                                          rows="4">{{ old('description_ru') }}</textarea>
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
                                       placeholder="მაგალითად: უკრაინული ბორშჩი" 
                                       class="input @error('name_ka') input-error @enderror" 
                                       id="name_ka" 
                                       value="{{ old('name_ka') }}" />
                                @error('name_ka')
                                    <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="label-text" for="description_ka">Описание (KA)</label>
                                <textarea name="description_ka" 
                                          placeholder="კერძის დეტალური აღწერა" 
                                          class="textarea textarea-bordered @error('description_ka') textarea-error @enderror" 
                                          id="description_ka" 
                                          rows="4">{{ old('description_ka') }}</textarea>
                                @error('description_ka')
                                    <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="label-text" for="image">Изображение</label>
                        <input type="file" 
                               name="image" 
                               accept="image/*"
                               class="input @error('image') file-input-error @enderror" />
                        <span class="text-base-content/60 text-xs mt-1 block">Максимальный размер: 2 МБ</span>
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
                                   value="{{ old('price') }}" 
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
                                   value="{{ old('discount_price') }}" 
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
                            <label class="label-text" for="weight_volume">Вес/Объем</label>
                            <input type="text" 
                                   name="weight_volume" 
                                   placeholder="Например: 350 г или 500 мл" 
                                   class="input @error('weight_volume') input-error @enderror" 
                                   id="weight_volume" 
                                   value="{{ old('weight_volume') }}" />
                            @error('weight_volume')
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
                                   value="{{ old('calories') }}" 
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
                                   value="{{ old('proteins') }}" 
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
                                   value="{{ old('fats') }}" 
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
                                   value="{{ old('carbohydrates') }}" 
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
                                   value="{{ old('fiber') }}" 
                                   step="0.01"
                                   min="0" />
                            @error('fiber')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Информация о соусе -->
                <div class="space-y-4 border-t border-base-content/10 pt-6">
                    <h4 class="text-base-content text-lg font-semibold">Информация о соусе (необязательно)</h4>
                    <p class="text-base-content/60 text-sm">Заполните эти поля, если к блюду подается соус с отдельной пищевой ценностью</p>
                    
                    <!-- Переводы названия соуса -->
                    <div class="space-y-4">
                        <!-- Русский язык -->
                        <div class="space-y-3 rounded-lg bg-base-200/50 p-4">
                            <div class="flex items-center gap-2">
                                <span class="icon-[tabler--bottle] size-5 text-primary"></span>
                                <h6 class="text-base-content font-medium">Название соуса (Русский)</h6>
                            </div>
                            
                            <div>
                                <label class="label-text" for="sauce_name_ru">Название соуса (RU)</label>
                                <input type="text" 
                                       name="sauce_name_ru" 
                                       placeholder="Например: Арахисовый соус" 
                                       class="input @error('sauce_name_ru') input-error @enderror" 
                                       id="sauce_name_ru" 
                                       value="{{ old('sauce_name_ru') }}" />
                                @error('sauce_name_ru')
                                    <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Грузинский язык -->
                        <div class="space-y-3 rounded-lg bg-base-200/50 p-4">
                            <div class="flex items-center gap-2">
                                <span class="icon-[tabler--bottle] size-5 text-primary"></span>
                                <h6 class="text-base-content font-medium">Название соуса (Грузинский)</h6>
                            </div>
                            
                            <div>
                                <label class="label-text" for="sauce_name_ka">Название соуса (KA)</label>
                                <input type="text" 
                                       name="sauce_name_ka" 
                                       placeholder="მაგალითად: არაქისის სოუსი" 
                                       class="input @error('sauce_name_ka') input-error @enderror" 
                                       id="sauce_name_ka" 
                                       value="{{ old('sauce_name_ka') }}" />
                                @error('sauce_name_ka')
                                    <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- КБЖУ соуса -->
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="label-text" for="sauce_weight_volume">Вес/Объем соуса</label>
                            <input type="text" 
                                   name="sauce_weight_volume" 
                                   placeholder="Например: 40 г или 30 мл" 
                                   class="input @error('sauce_weight_volume') input-error @enderror" 
                                   id="sauce_weight_volume" 
                                   value="{{ old('sauce_weight_volume') }}" />
                            @error('sauce_weight_volume')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="label-text" for="sauce_calories">Калории соуса (ккал)</label>
                            <input type="number" 
                                   name="sauce_calories" 
                                   placeholder="0" 
                                   class="input @error('sauce_calories') input-error @enderror" 
                                   id="sauce_calories" 
                                   value="{{ old('sauce_calories') }}" 
                                   min="0" />
                            @error('sauce_calories')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                        <div>
                            <label class="label-text" for="sauce_proteins">Белки соуса (г)</label>
                            <input type="number" 
                                   name="sauce_proteins" 
                                   placeholder="0.00" 
                                   class="input @error('sauce_proteins') input-error @enderror" 
                                   id="sauce_proteins" 
                                   value="{{ old('sauce_proteins') }}" 
                                   step="0.01"
                                   min="0" />
                            @error('sauce_proteins')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="label-text" for="sauce_fats">Жиры соуса (г)</label>
                            <input type="number" 
                                   name="sauce_fats" 
                                   placeholder="0.00" 
                                   class="input @error('sauce_fats') input-error @enderror" 
                                   id="sauce_fats" 
                                   value="{{ old('sauce_fats') }}" 
                                   step="0.01"
                                   min="0" />
                            @error('sauce_fats')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="label-text" for="sauce_carbohydrates">Углеводы соуса (г)</label>
                            <input type="number" 
                                   name="sauce_carbohydrates" 
                                   placeholder="0.00" 
                                   class="input @error('sauce_carbohydrates') input-error @enderror" 
                                   id="sauce_carbohydrates" 
                                   value="{{ old('sauce_carbohydrates') }}" 
                                   step="0.01"
                                   min="0" />
                            @error('sauce_carbohydrates')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="label-text" for="sauce_fiber">Клетчатка соуса (г)</label>
                            <input type="number" 
                                   name="sauce_fiber" 
                                   placeholder="0.00" 
                                   class="input @error('sauce_fiber') input-error @enderror" 
                                   id="sauce_fiber" 
                                   value="{{ old('sauce_fiber') }}" 
                                   step="0.01"
                                   min="0" />
                            @error('sauce_fiber')
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
                               value="{{ old('sort_order', 0) }}" 
                               min="0" />
                        @error('sort_order')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn btn-lg btn-primary btn-gradient">
                        <span class="icon-[tabler--check] size-5"></span>
                        Создать блюдо
                    </button>

                    <a href="{{ route('admin.dishes.index') }}" class="btn btn-lg btn-outline">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
