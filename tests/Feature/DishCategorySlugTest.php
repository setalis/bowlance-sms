<?php

use App\Models\DishCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('automatically generates slug when creating category without slug', function () {
    $category = DishCategory::create([
        'name' => 'Горячие блюда',
    ]);

    expect($category->slug)->toBe('goriacie-bliuda');
});

it('keeps custom slug when provided', function () {
    $category = DishCategory::create([
        'name' => 'Горячие блюда',
        'slug' => 'custom-slug',
    ]);

    expect($category->slug)->toBe('custom-slug');
});

it('generates unique slug when duplicate exists', function () {
    DishCategory::create([
        'name' => 'Салаты',
        'slug' => 'salaty',
    ]);

    $category = DishCategory::create([
        'name' => 'Салаты',
    ]);

    expect($category->slug)->toBe('salaty-1');
});

it('generates slug from english name', function () {
    $category = DishCategory::create([
        'name' => 'Hot Dishes',
    ]);

    expect($category->slug)->toBe('hot-dishes');
});

it('handles special characters in slug generation', function () {
    $category = DishCategory::create([
        'name' => 'Блюда & Закуски!',
    ]);

    expect($category->slug)->toBe('bliuda-zakuski');
});

it('generates slug on update when slug is empty', function () {
    $category = DishCategory::create([
        'name' => 'Десерты',
        'slug' => 'deserty',
    ]);

    $category->update([
        'name' => 'Сладости',
        'slug' => '',
    ]);

    expect($category->fresh()->slug)->toBe('sladosti');
});

it('can manually generate slug using method', function () {
    $category = new DishCategory;
    $slug = $category->generateSlug('Супы и бульоны');

    expect($slug)->toBe('supy-i-bulyony');
});
