<?php

use App\Models\ConstructorCategory;

test('constructor category can be created with icon class', function () {
    $category = ConstructorCategory::create([
        'name_ru' => 'Основа',
        'icon_class' => 'icon-[tabler--tools-kitchen-2]',
        'sort_order' => 0,
    ]);

    expect($category->icon_class)->toBe('icon-[tabler--tools-kitchen-2]');
});

test('constructor category icon class is nullable', function () {
    $category = ConstructorCategory::create([
        'name_ru' => 'Категория без иконки',
        'sort_order' => 0,
    ]);

    expect($category->icon_class)->toBeNull();
});

test('constructor category icon displays default when empty', function () {
    $category = ConstructorCategory::create([
        'name_ru' => 'Категория',
        'sort_order' => 0,
    ]);

    $iconClass = $category->icon_class ?: 'icon-[tabler--tools-kitchen-2]';

    expect($iconClass)->toBe('icon-[tabler--tools-kitchen-2]');
});

test('constructor category icon displays custom when set', function () {
    $category = ConstructorCategory::create([
        'name_ru' => 'Категория',
        'icon_class' => 'icon-[tabler--leaf]',
        'sort_order' => 0,
    ]);

    $iconClass = $category->icon_class ?: 'icon-[tabler--tools-kitchen-2]';

    expect($iconClass)->toBe('icon-[tabler--leaf]');
});

