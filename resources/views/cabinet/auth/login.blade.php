@extends('layouts.guest')

@section('title', 'Личный кабинет — Вход — ' . config('app.name'))

@section('content')
    <div class="auth-background flex h-auto min-h-screen items-center justify-center overflow-x-hidden bg-cover bg-center bg-no-repeat py-10">
        <div class="relative flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="bg-base-100 shadow-base-300/20 z-1 w-full space-y-6 rounded-xl p-6 shadow-md sm:max-w-md lg:p-8">
                <div class="flex items-center gap-3">
                    <a href="{{ url('/') }}" class="flex items-center gap-3">
                        <span class="text-primary">
                            <svg width="32" height="32" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip0_cabinet)">
                                    <path d="M25.5 0H8.5C3.80558 0 0 3.80558 0 8.5V25.5C0 30.1944 3.80558 34 8.5 34H25.5C30.1944 34 34 30.1944 34 25.5V8.5C34 3.80558 30.1944 0 25.5 0Z" fill="url(#paint0_cabinet)" />
                                </g>
                                <defs>
                                    <linearGradient id="paint0_cabinet" x1="30.28" y1="2.66" x2="4.25" y2="32.41" gradientUnits="userSpaceOnUse">
                                        <stop offset="0" stop-color="currentColor" />
                                        <stop offset="1" stop-color="currentColor" />
                                    </linearGradient>
                                    <clipPath id="clip0_cabinet"><rect width="34" height="34" fill="white" /></clipPath>
                                </defs>
                            </svg>
                        </span>
                        <h2 class="text-base-content text-xl font-bold">{{ config('app.name') }}</h2>
                    </a>
                </div>
                <div>
                    <h3 class="text-base-content mb-1.5 text-2xl font-semibold">Личный кабинет</h3>
                    <p class="text-base-content/80">Войдите по номеру телефона</p>
                </div>
                <div class="space-y-4">
                    <form class="mb-4 space-y-4" action="/cabinet/login" method="POST" accept-charset="UTF-8">
                        @csrf
                        <div>
                            <label class="label-text" for="userPhone">Телефон *</label>
                            <input type="text" name="phone" placeholder="+995 5XX XXX XXX" class="input @error('phone') input-error @enderror" id="userPhone" value="{{ old('phone') }}" required autocomplete="tel" />
                            @error('phone')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="label-text" for="userPassword">Пароль *</label>
                            <div class="input @error('password') input-error @enderror">
                                <input id="userPassword" name="password" type="password" placeholder="············" required />
                                <button type="button" data-toggle-password='{ "target": "#userPassword" }' class="block cursor-pointer" aria-label="Показать пароль">
                                    <span class="icon-[tabler--eye] password-active:block hidden size-5 shrink-0"></span>
                                    <span class="icon-[tabler--eye-off] password-active:hidden block size-5 shrink-0"></span>
                                </button>
                            </div>
                            @error('password')
                                <span class="text-error text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" class="checkbox checkbox-primary checkbox-sm" id="rememberMe" name="remember" />
                            <label class="label-text text-base-content/80 p-0 text-base" for="rememberMe">Запомнить меня</label>
                        </div>
                        <button class="btn btn-lg btn-primary btn-gradient btn-block" type="submit">Войти</button>
                    </form>
                    <p class="text-base-content/80 text-center">
                        <a href="{{ route('home') }}" class="link link-animated link-primary font-normal">На главную</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
