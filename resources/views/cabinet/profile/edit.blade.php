@extends('layouts.cabinet')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ $title }}</h1>
    </div>

    @session('status')
        <div class="alert alert-soft alert-success flex items-center gap-2 mb-6 border-0" role="alert">
            <span class="icon-[tabler--circle-check] size-5"></span>
            <span>{{ $value }}</span>
        </div>
    @endsession

    <div class="card max-w-2xl">
        <div class="card-body">
            <form method="POST" action="{{ route('cabinet.profile.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="label-text" for="name">Имя *</label>
                        <input type="text" id="name" name="name" class="input input-bordered w-full"
                            placeholder="Введите имя" value="{{ old('name', $user->name) }}" required autofocus />
                        @error('name')
                            <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="label-text" for="email">Email *</label>
                        <input type="email" id="email" name="email" class="input input-bordered w-full"
                            placeholder="email@example.com" value="{{ old('email', $user->email) }}" required />
                        @error('email')
                            <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="label-text" for="phone">Телефон *</label>
                    <input type="text" id="phone" name="phone" class="input input-bordered w-full"
                        placeholder="+995 5XX XXX XXX" value="{{ old('phone', $user->phone) }}" required />
                    @error('phone')
                        <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-base-content/60">Телефон используется для входа в личный кабинет и должен быть уникальным.</p>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                    <a href="{{ route('cabinet.dashboard') }}" class="btn btn-ghost">Отмена</a>
                </div>
            </form>
        </div>
    </div>
@endsection
