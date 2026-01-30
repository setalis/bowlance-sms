@extends('layouts.cabinet')

@section('content')
    <div class="mb-6">
        <a href="{{ route('cabinet.addresses.index') }}" class="btn btn-ghost btn-sm mb-2 gap-2">
            <span class="icon-[tabler--arrow-left] size-4"></span>
            Назад к адресам
        </a>
        <h1 class="text-2xl font-bold">{{ $title }}</h1>
    </div>

    <div class="card max-w-2xl">
        <div class="card-body">
            <form action="{{ route('cabinet.addresses.update', $address) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="label">
                            <span class="label-text">Название адреса <span class="text-error">*</span></span>
                        </label>
                        <input type="text" 
                               name="label" 
                               value="{{ old('label', $address->label) }}" 
                               placeholder="Дом, Работа, и т.д."
                               class="input input-bordered w-full @error('label') input-error @enderror"
                               required>
                        @error('label')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div>
                        <label class="label">
                            <span class="label-text">Адрес <span class="text-error">*</span></span>
                        </label>
                        <textarea name="address" 
                                  rows="3"
                                  placeholder="Введите полный адрес доставки"
                                  class="textarea textarea-bordered w-full @error('address') textarea-error @enderror"
                                  required>{{ old('address', $address->address) }}</textarea>
                        @error('address')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" 
                                   name="is_default" 
                                   value="1"
                                   {{ old('is_default', $address->is_default) ? 'checked' : '' }}
                                   class="checkbox checkbox-primary">
                            <span class="label-text">Использовать этот адрес по умолчанию</span>
                        </label>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="btn btn-primary gap-2">
                            <span class="icon-[tabler--check] size-5"></span>
                            Сохранить изменения
                        </button>
                        <a href="{{ route('cabinet.addresses.index') }}" class="btn btn-ghost">
                            Отмена
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
