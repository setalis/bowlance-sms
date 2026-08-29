<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the homepage hero slider with responsive picture sources', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('<picture>', false);
    $response->assertSee(asset('storage/images/slider/slider-m-1.jpg'), false);
    $response->assertSee(asset('storage/images/slider/slider-d-1.jpg'), false);
    $response->assertSee(asset('storage/images/slider/slider-m-2.jpg'), false);
    $response->assertSee(asset('storage/images/slider/slider-d-2.jpg'), false);
    $response->assertSee(asset('storage/images/slider/slider-m-3.jpg'), false);
    $response->assertSee(asset('storage/images/slider/slider-d-3.jpg'), false);
    $response->assertSee(asset('storage/images/slider/slider-m-1.webp'), false);
    $response->assertSee(asset('storage/images/slider/slider-d-1.webp'), false);
    $response->assertSee(asset('storage/images/slider/slider-m-2.webp'), false);
    $response->assertSee(asset('storage/images/slider/slider-d-2.webp'), false);
    $response->assertSee(asset('storage/images/slider/slider-m-3.webp'), false);
    $response->assertSee(asset('storage/images/slider/slider-d-3.webp'), false);
});

it('shows the first hero slide immediately without flyonui loading classes', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('class="carousel-body h-full"', false);
    $response->assertDontSee('carousel-body h-full opacity-0', false);
    $response->assertDontSee('loadingClasses', false);
});

it('prioritizes the first hero slide and lazy-loads the rest', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('fetchpriority="high"', false);
    $response->assertSee('loading="lazy"', false);
    $response->assertSee('fetchpriority="low"', false);
});

it('preloads the first hero webp for mobile and desktop', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('rel="preload"', false);
    $response->assertSee('as="image"', false);
    $response->assertSee(asset('storage/images/slider/slider-m-1.webp'), false);
    $response->assertSee(asset('storage/images/slider/slider-d-1.webp'), false);
    $response->assertSee('media="(max-width: 767px)"', false);
    $response->assertSee('media="(min-width: 768px)"', false);
});
