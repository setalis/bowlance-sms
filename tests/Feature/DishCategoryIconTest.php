<?php

use App\Models\DishCategory;
use App\Models\User;

test('dish category can be created with icon class', function () {
    $category = DishCategory::create([
        'name_ru' => 'Тестовая категория',
        'icon_class' => 'icon-[tabler--bowl-chopsticks]',
        'is_active' => true,
        'sort' => 0,
    ]);

    expect($category->icon_class)->toBe('icon-[tabler--bowl-chopsticks]');
});

test('dish category icon class is nullable', function () {
    $category = DishCategory::create([
        'name_ru' => 'Категория без иконки',
        'is_active' => true,
        'sort' => 0,
    ]);

    expect($category->icon_class)->toBeNull();
});

test('dish category icon displays default when empty', function () {
    $category = DishCategory::create([
        'name_ru' => 'Категория',
        'is_active' => true,
        'sort' => 0,
    ]);

    $iconClass = $category->icon_class ?: 'icon-[tabler--bowl-chopsticks]';

    expect($iconClass)->toBe('icon-[tabler--bowl-chopsticks]');
});

test('dish category icon displays custom when set', function () {
    $category = DishCategory::create([
        'name_ru' => 'Категория',
        'icon_class' => 'icon-[tabler--salad]',
        'is_active' => true,
        'sort' => 0,
    ]);

    $iconClass = $category->icon_class ?: 'icon-[tabler--bowl-chopsticks]';

    expect($iconClass)->toBe('icon-[tabler--salad]');
});

