@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
            <a href="{{ route('admin.dish-addons.index') }}" class="btn btn-text btn-sm mb-4">
                <span class="icon-[tabler--arrow-left] size-4"></span>
                Назад к списку
            </a>
        </div>

        <div class="bg-base-100 shadow-base-300/20 w-full space-y-6 rounded-xl p-6 shadow-md lg:p-8">
            <div>
                <h3 class="text-base-content mb-1.5 text-2xl font-semibold">Создать добавку</h3>
                <p class="text-base-content/80">Добавка из справочника привязывается к блюдам отдельно</p>
            </div>

            <form action="{{ route('admin.dish-addons.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="label-text" for="name_ru">Название (RU)*</label>
                        <input type="text"
                               name="name_ru"
                               class="input @error('name_ru') input-error @enderror"
                               id="name_ru"
                               value="{{ old('name_ru') }}"
                               required />
                        @error('name_ru')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="label-text" for="name_ka">Название (KA)</label>
                        <input type="text"
                               name="name_ka"
                               class="input @error('name_ka') input-error @enderror"
                               id="name_ka"
                               value="{{ old('name_ka') }}" />
                        @error('name_ka')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="label-text" for="price">Цена по умолчанию*</label>
                        <input type="number"
                               name="price"
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
                        <label class="label-text" for="calories">Калории (ккал)</label>
                        <input type="number"
                               name="calories"
                               class="input @error('calories') input-error @enderror"
                               id="calories"
                               value="{{ old('calories') }}"
                               placeholder="0"
                               min="0" />
                        @error('calories')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="label-text" for="proteins">Белки (г)</label>
                            <input type="number"
                                   name="proteins"
                                   class="input @error('proteins') input-error @enderror"
                                   id="proteins"
                                   value="{{ old('proteins') }}"
                                   placeholder="0.00"
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
                                   class="input @error('fats') input-error @enderror"
                                   id="fats"
                                   value="{{ old('fats') }}"
                                   placeholder="0.00"
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
                                   class="input @error('carbohydrates') input-error @enderror"
                                   id="carbohydrates"
                                   value="{{ old('carbohydrates') }}"
                                   placeholder="0.00"
                                   step="0.01"
                                   min="0" />
                            @error('carbohydrates')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="label-text" for="sort_order">Сортировка</label>
                        <input type="number"
                               name="sort_order"
                               class="input @error('sort_order') input-error @enderror"
                               id="sort_order"
                               value="{{ old('sort_order', 0) }}"
                               min="0" />
                    </div>

                    <label class="label cursor-pointer justify-start gap-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               class="checkbox"
                               @checked(old('is_active', true)) />
                        <span class="label-text">Активна</span>
                    </label>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn btn-lg btn-primary btn-gradient">
                        <span class="icon-[tabler--check] size-5"></span>
                        Создать
                    </button>
                    <a href="{{ route('admin.dish-addons.index') }}" class="btn btn-lg btn-outline">Отмена</a>
                </div>
            </form>
        </div>
    </div>
@endsection
