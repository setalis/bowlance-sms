@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-text btn-sm mb-4">
                <span class="icon-[tabler--arrow-left] size-4"></span>
                Назад к списку
            </a>
        </div>

        <div class="bg-base-100 shadow-base-300/20 w-full space-y-6 rounded-xl p-6 shadow-md lg:p-8">
            <div>
                <h3 class="text-base-content mb-1.5 text-2xl font-semibold">Создать категорию</h3>
                <p class="text-base-content/80">Добавьте новую категорию блюд в меню</p>
            </div>

            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Основная информация -->
                <div class="space-y-4">
                    <h4 class="text-base-content text-lg font-semibold">Основная информация</h4>
                    
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="label-text" for="name">Название*</label>
                            <input type="text" 
                                   name="name" 
                                   placeholder="Например: Горячие блюда" 
                                   class="input @error('name') input-error @enderror" 
                                   id="name" 
                                   value="{{ old('name') }}" 
                                   required />
                            @error('name')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="label-text" for="slug">Slug</label>
                            <input type="text" 
                                   name="slug" 
                                   placeholder="goriachie-bliuda (оставьте пустым для автогенерации)" 
                                   class="input @error('slug') input-error @enderror" 
                                   id="slug" 
                                   value="{{ old('slug') }}" />
                            <span class="text-base-content/60 text-xs mt-1 block">Оставьте пустым для автоматической генерации</span>
                            @error('slug')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="label-text" for="description">Описание</label>
                        <textarea name="description" 
                                  placeholder="Краткое описание категории" 
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
                               class="input @error('image') file-input-error @enderror"  />
                        <span class="text-base-content/60 text-xs mt-1 block">Максимальный размер: 2 МБ</span>
                        @error('image')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror

                        


                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="label-text" for="sort">Сортировка</label>
                            <input type="number" 
                                   name="sort" 
                                   placeholder="0" 
                                   class="input @error('sort') input-error @enderror" 
                                   id="sort" 
                                   value="{{ old('sort', 0) }}" 
                                   min="0" />
                            @error('sort')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="label cursor-pointer justify-start gap-3">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" 
                                       name="is_active" 
                                       class="checkbox" 
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }} />
                                <span class="label-text">Активна</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- SEO настройки -->
                <div class="space-y-4 border-t border-base-content/10 pt-6">
                    <h4 class="text-base-content text-lg font-semibold">SEO настройки</h4>
                    
                    <div>
                        <label class="label-text" for="meta_title">Meta Title</label>
                        <input type="text" 
                               name="meta_title" 
                               placeholder="SEO заголовок" 
                               class="input @error('meta_title') input-error @enderror" 
                               id="meta_title" 
                               value="{{ old('meta_title') }}" />
                        @error('meta_title')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="label-text" for="meta_description">Meta Description</label>
                        <textarea name="meta_description" 
                                  placeholder="SEO описание" 
                                  class="textarea textarea-bordered @error('meta_description') textarea-error @enderror" 
                                  id="meta_description" 
                                  rows="2">{{ old('meta_description') }}</textarea>
                        @error('meta_description')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="label-text" for="meta_keywords">Meta Keywords</label>
                        <input type="text" 
                               name="meta_keywords" 
                               placeholder="ключевые, слова, через, запятую" 
                               class="input @error('meta_keywords') input-error @enderror" 
                               id="meta_keywords" 
                               value="{{ old('meta_keywords') }}" />
                        @error('meta_keywords')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div>
                            <label class="label-text" for="meta_url">Meta URL</label>
                            <input type="url" 
                                   name="meta_url" 
                                   placeholder="https://example.com" 
                                   class="input @error('meta_url') input-error @enderror" 
                                   id="meta_url" 
                                   value="{{ old('meta_url') }}" />
                            @error('meta_url')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="label-text" for="meta_type">Meta Type</label>
                            <input type="text" 
                                   name="meta_type" 
                                   placeholder="website" 
                                   class="input @error('meta_type') input-error @enderror" 
                                   id="meta_type" 
                                   value="{{ old('meta_type') }}" />
                            @error('meta_type')
                                <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="label-text" for="meta_image">Meta Image URL</label>
                        <input type="text" 
                               name="meta_image" 
                               placeholder="https://example.com/image.jpg" 
                               class="input @error('meta_image') input-error @enderror" 
                               id="meta_image" 
                               value="{{ old('meta_image') }}" />
                        @error('meta_image')
                            <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn btn-lg btn-primary btn-gradient">
                        <span class="icon-[tabler--check] size-5"></span>
                        Создать категорию
                    </button>

                    <a href="{{ route('admin.categories.index') }}" class="btn btn-lg btn-outline">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
