@extends('layouts.front.app')

@section('content')
    <section class="container mx-auto flex min-h-[70vh] items-center px-4 py-10">
        <div class="mx-auto w-full max-w-3xl">
            <div class="mb-8 text-center">
                <img src="{{ asset('storage/images/logo.png') }}" alt="Bowlance" class="mx-auto mb-4 size-20">
                <h1 class="text-3xl font-bold text-base-content sm:text-4xl">{{ __('frontend.order_mode_title') }}</h1>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach(\App\Enums\OrderMode::cases() as $modeOption)
                    <form method="POST" action="{{ route('order-mode.store') }}" class="h-full">
                        @csrf
                        <input type="hidden" name="order_mode" value="{{ $modeOption->value }}">
                        <button type="submit"
                                class="flex h-full w-full flex-col items-start gap-3 rounded-3xl border-2 border-base-content/10 bg-base-100 p-6 text-left shadow-sm transition hover:border-emerald-600 hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                            <span class="{{ $modeOption === \App\Enums\OrderMode::Onsite ? 'icon-[tabler--building-store]' : 'icon-[tabler--truck-delivery]' }} size-10 text-emerald-600"></span>
                            <span class="text-xl font-bold">{{ $modeOption->label() }}</span>
                            <span class="text-sm text-base-content/70">
                                {{ $modeOption === \App\Enums\OrderMode::Onsite ? __('frontend.order_mode_onsite_hint') : __('frontend.order_mode_delivery_hint') }}
                            </span>
                        </button>
                    </form>
                @endforeach
            </div>

            @error('order_mode')
                <p class="mt-4 text-center text-sm text-error">{{ $message }}</p>
            @enderror
        </div>
    </section>
@endsection
