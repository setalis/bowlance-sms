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

                    <!-- Детали адреса -->
                    <div class="divider">Дополнительная информация</div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label">
                                <span class="label-text">Подъезд</span>
                            </label>
                            <input type="text" 
                                   name="entrance" 
                                   value="{{ old('entrance', $address->entrance) }}" 
                                   placeholder="1"
                                   class="input input-bordered w-full @error('entrance') input-error @enderror">
                            @error('entrance')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <div>
                            <label class="label">
                                <span class="label-text">Этаж</span>
                            </label>
                            <input type="text" 
                                   name="floor" 
                                   value="{{ old('floor', $address->floor) }}" 
                                   placeholder="5"
                                   class="input input-bordered w-full @error('floor') input-error @enderror">
                            @error('floor')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label">
                                <span class="label-text">Квартира</span>
                            </label>
                            <input type="text" 
                                   name="apartment" 
                                   value="{{ old('apartment', $address->apartment) }}" 
                                   placeholder="42"
                                   class="input input-bordered w-full @error('apartment') input-error @enderror">
                            @error('apartment')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>

                        <div>
                            <label class="label">
                                <span class="label-text">Домофон</span>
                            </label>
                            <input type="text" 
                                   name="intercom" 
                                   value="{{ old('intercom', $address->intercom) }}" 
                                   placeholder="42К"
                                   class="input input-bordered w-full @error('intercom') input-error @enderror">
                            @error('intercom')
                                <label class="label">
                                    <span class="label-text-alt text-error">{{ $message }}</span>
                                </label>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="label">
                            <span class="label-text">Комментарий курьеру</span>
                        </label>
                        <textarea name="courier_comment" 
                                  rows="2"
                                  placeholder="Например: позвоните за 5 минут"
                                  class="textarea textarea-bordered w-full @error('courier_comment') textarea-error @enderror">{{ old('courier_comment', $address->courier_comment) }}</textarea>
                        @error('courier_comment')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div>
                        <label class="label">
                            <span class="label-text">Телефон получателя</span>
                        </label>
                        <input type="tel" 
                               name="receiver_phone" 
                               value="{{ old('receiver_phone', $address->receiver_phone) }}" 
                               placeholder="+995 555 12 34 56"
                               class="input input-bordered w-full @error('receiver_phone') input-error @enderror">
                        @error('receiver_phone')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="checkbox" 
                                   name="leave_at_door" 
                                   value="1"
                                   {{ old('leave_at_door', $address->leave_at_door) ? 'checked' : '' }}
                                   class="toggle toggle-primary">
                            <span class="label-text">Оставить у двери</span>
                        </label>
                    </div>

                    <div class="divider"></div>

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
