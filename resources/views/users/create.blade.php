@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-text btn-sm mb-4">
                <span class="icon-[tabler--arrow-left] size-4"></span>
                Назад к списку
            </a>
        </div>

        <div class="bg-base-100 shadow-base-300/20 w-full space-y-6 rounded-xl p-6 shadow-md lg:p-8">
            <div>
                <h3 class="text-base-content mb-1.5 text-2xl font-semibold">Создать пользователя</h3>
                <p class="text-base-content/80">Добавьте нового пользователя в систему</p>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="label-text" for="userName">Имя*</label>
                    <input type="text" 
                           name="name" 
                           placeholder="Введите имя" 
                           class="input @error('name') input-error @enderror" 
                           id="userName" 
                           value="{{ old('name') }}" 
                           required />
                    @error('name')
                        <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="label-text" for="userEmail">Email адрес*</label>
                    <input type="email" 
                           name="email" 
                           placeholder="Введите email адрес" 
                           class="input @error('email') input-error @enderror" 
                           id="userEmail" 
                           value="{{ old('email') }}" 
                           required />
                    @error('email')
                        <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="label-text" for="userPhone">Телефон</label>
                    <input type="tel" 
                           name="phone" 
                           placeholder="+995 555 123 456" 
                           class="input @error('phone') input-error @enderror" 
                           id="userPhone" 
                           value="{{ old('phone') }}" />
                    @error('phone')
                        <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="label-text" for="userPassword">Пароль*</label>
                    <input type="password" 
                           name="password" 
                           placeholder="Введите пароль" 
                           class="input @error('password') input-error @enderror" 
                           id="userPassword" 
                           required />
                    @error('password')
                        <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="label-text" for="userPasswordConfirmation">Подтверждение пароля*</label>
                    <input type="password" 
                           name="password_confirmation" 
                           placeholder="Подтвердите пароль" 
                           class="input @error('password_confirmation') input-error @enderror" 
                           id="userPasswordConfirmation" 
                           required />
                    @error('password_confirmation')
                        <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="label-text" for="userRole">Роль*</label>
                    <select name="role" 
                            class="select select-bordered w-full @error('role') select-error @enderror" 
                            id="userRole" 
                            required>
                        <option value="">Выберите роль</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->value }}" {{ old('role') === $role->value ? 'selected' : '' }}>
                                {{ $role === \App\Enums\UserRole::Admin ? 'Администратор' : 'Пользователь' }}
                            </option>
                        @endforeach
                    </select>
                    @error('role')
                        <span class="text-error text-sm mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn btn-lg btn-primary btn-gradient">
                        <span class="icon-[tabler--check] size-5"></span>
                        Создать пользователя
                    </button>

                    <a href="{{ route('admin.users.index') }}" class="btn btn-lg btn-outline">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
