<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>419 — Сессия истекла | {{ config('app.name') }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-base-200 min-h-screen flex flex-col items-center justify-center p-4">
    <div class="card max-w-md w-full">
        <div class="card-body text-center">
            <span class="text-primary inline-block mb-4">
                <span class="icon-[tabler--clock-off] size-16"></span>
            </span>
            <h1 class="text-2xl font-bold mb-2">419 — Сессия истекла</h1>
            <p class="text-base-content/80 mb-6">
                Страница была открыта слишком долго или токен безопасности устарел. Обновите страницу и повторите попытку.
            </p>
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="javascript:location.reload()" class="btn btn-primary">Обновить страницу</a>
                <a href="{{ url('/') }}" class="btn btn-ghost">На главную</a>
                @if(request()->is('cabinet*'))
                    <a href="{{ route('cabinet.login') }}" class="btn btn-ghost">Вход в кабинет</a>
                @elseif(request()->is('login') || request()->is('register'))
                    <a href="{{ route('login') }}" class="btn btn-ghost">Вход</a>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
