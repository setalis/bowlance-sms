@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
            <a href="{{ route('admin.constructor-products.index') }}" class="btn btn-text btn-sm mb-4">
                <span class="icon-[tabler--arrow-left] size-4"></span>
                Назад к списку
            </a>
        </div>

        <div class="bg-base-100 shadow-base-300/20 w-full space-y-6 rounded-xl p-6 shadow-md lg:p-8">
            <div>
                <h3 class="text-base-content mb-1.5 text-2xl font-semibold">Создать продукт конструктора</h3>
                <p class="text-base-content/80">Добавьте новый продукт для конструктора боулов</p>
            </div>

            <form action="{{ route('admin.constructor-products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="space-y-4">
                    <h4 class="text-base-content text-lg font-semibold">Основная информация</h4>
                    
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="label-text" for="name">Название*</label>
                            <input type="text" 
                                   name="name" 
                                   placeholder="Например: Рис" 
                                   class="input @error('name') input-error @enderror" 
                                   id="name" 
                                   value="{{ old('name') }}" 
                                   required />
                            @error('name')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="label-text" for="constructor_category_id">Категория*</label>
                            <select name="constructor_category_id" 
                                    class="select @error('constructor_category_id') select-error @enderror" 
                                    id="constructor_category_id" 
                                    required>
                                <option value="">Выберите категорию</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('constructor_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('constructor_category_id')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="label-text" for="description">Описание</label>
                        <textarea name="description" 
                                  placeholder="Подробное описание продукта" 
                                  class="textarea textarea-bordered @error('description') textarea-error @enderror" 
                                  id="description" 
                                  rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror
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
                </div>

                <div class="space-y-4 border-t border-base-content/10 pt-6">
                    <h4 class="text-base-content text-lg font-semibold">Пищевая ценность</h4>
                    
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="label-text" for="weight_volume">Вес/Объем</label>
                            <input type="text" 
                                   name="weight_volume" 
                                   placeholder="Например: 100 г" 
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
                        Создать продукт
                    </button>

                    <a href="{{ route('admin.constructor-products.index') }}" class="btn btn-lg btn-outline">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
