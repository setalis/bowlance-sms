@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
            <a href="{{ route('admin.constructor-categories.index') }}" class="btn btn-text btn-sm mb-4">
                <span class="icon-[tabler--arrow-left] size-4"></span>
                Назад к списку
            </a>
        </div>

        <div class="bg-base-100 shadow-base-300/20 w-full space-y-6 rounded-xl p-6 shadow-md lg:p-8">
            <div>
                <h3 class="text-base-content mb-1.5 text-2xl font-semibold">Создать категорию конструктора</h3>
                <p class="text-base-content/80">Добавьте новую категорию для конструктора боулов</p>
            </div>

            <form action="{{ route('admin.constructor-categories.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label class="label-text" for="name">Название*</label>
                        <input type="text" 
                               name="name" 
                               placeholder="Например: Основа" 
                               class="input @error('name') input-error @enderror" 
                               id="name" 
                               value="{{ old('name') }}" 
                               required />
                        @error('name')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

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
                        Создать категорию
                    </button>

                    <a href="{{ route('admin.constructor-categories.index') }}" class="btn btn-lg btn-outline">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
