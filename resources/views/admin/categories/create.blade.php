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

                    <div>
                        <label class="label-text" for="icon_class">Класс иконки</label>
                        <input type="text" 
                               name="icon_class" 
                               placeholder="icon-[tabler--bowl-chopsticks]" 
                               class="input @error('icon_class') input-error @enderror" 
                               id="icon_class" 
                               value="{{ old('icon_class') }}" />
                        <span class="text-base-content/60 text-xs mt-1 block">
                            Класс иконки Tabler Icons (например: icon-[tabler--bowl-chopsticks]). 
                            <a href="https://tabler.io/icons" target="_blank" class="text-primary hover:underline">Смотреть иконки</a>
                        </span>
                        @error('icon_class')
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
                                       placeholder="Например: Горячие блюда" 
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
                                          placeholder="Краткое описание категории" 
                                          class="textarea textarea-bordered @error('description_ru') textarea-error @enderror" 
                                          id="description_ru" 
                                          rows="3">{{ old('description_ru') }}</textarea>
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
                                       placeholder="მაგალითად: ცხელი კერძები" 
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
                                          placeholder="კატეგორიის მოკლე აღწერა" 
                                          class="textarea textarea-bordered @error('description_ka') textarea-error @enderror" 
                                          id="description_ka" 
                                          rows="3">{{ old('description_ka') }}</textarea>
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
