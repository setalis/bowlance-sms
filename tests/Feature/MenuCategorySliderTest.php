<?php

use App\Models\DishCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('показывает мобильный слайдер категорий меню на главной странице', function () {
    DishCategory::factory()->active()->create([
        'name_ru' => 'Завтраки',
        'sort' => 1,
    ]);

    DishCategory::factory()->active()->create([
        'name_ru' => 'Основные блюда',
        'sort' => 2,
    ]);

    DishCategory::factory()->active()->create([
        'name_ru' => 'Салаты',
        'sort' => 3,
    ]);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('overflow-x-clip overscroll-x-none max-w-full', false);
    $response->assertSee('menu-category-slider', false);
    $response->assertSee('overflow-x-auto overscroll-x-contain snap-x snap-mandatory', false);
    $response->assertSee('w-max min-w-full flex-nowrap gap-2', false);
    $response->assertSee('Завтраки', false);
    $response->assertSee('Основные блюда', false);
    $response->assertSee('Салаты', false);
    $response->assertSee('hidden md:flex flex-wrap gap-2', false);
});

it('блокирует горизонтальный сдвиг страницы на витрине', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('class="overflow-x-clip overscroll-x-none max-w-full"', false);
    $response->assertSee('<body class="overflow-x-clip overscroll-x-none max-w-full"', false);
    $response->assertSee('inset-x-0 w-full', false);
});

it('не показывает слайдер категорий когда категории отсутствуют', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertDontSee('menu-category-slider', false);
});
