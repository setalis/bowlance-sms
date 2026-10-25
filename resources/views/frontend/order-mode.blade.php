@extends('layouts.front.app')

@section('content')
    <section class="flex min-h-[70vh] items-center">
        <div class="relative mx-auto w-full max-w-3xl overflow-hidden rounded-3xl">
            <picture>
                <source media="(min-width: 768px)" type="image/webp" srcset="{{ asset('storage/images/slider/slider-d-3.webp') }}">
                <source media="(min-width: 768px)" srcset="{{ asset('storage/images/slider/slider-d-3.jpg') }}">
                <source type="image/webp" srcset="{{ asset('storage/images/slider/slider-m-3.webp') }}">
                <img src="{{ asset('storage/images/slider/slider-m-3.jpg') }}"
                     alt=""
                     class="pointer-events-none absolute bottom-0 left-1/2 h-[240%] w-auto max-w-none -translate-x-1/2 md:bottom-auto md:left-auto md:right-0 md:top-1/2 md:h-[170%] md:translate-x-0 md:-translate-y-1/2">
            </picture>
            <div class="absolute inset-0 bg-white/30"></div>

            <div class="relative z-10 px-6 py-10 sm:px-10">
                <div class="mb-8 text-center">
                    <img src="{{ asset('storage/images/logo.png') }}" alt="Bowlance" class="mx-auto size-20">
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @foreach(\App\Enums\OrderMode::cases() as $modeOption)
                        <form method="POST" action="{{ route('order-mode.store') }}" class="h-full">
                            @csrf
                            <input type="hidden" name="order_mode" value="{{ $modeOption->value }}">
                            <button type="submit"
                                    class="flex h-full w-full flex-col items-start gap-3 rounded-3xl border border-white/70 bg-white/55 p-6 text-left shadow-lg backdrop-blur-md transition hover:border-emerald-600 hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
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
        </div>
    </section>
@endsection
