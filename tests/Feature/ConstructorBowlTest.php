<?php

use App\Models\ConstructorCategory;
use App\Models\ConstructorProduct;

it('displays constructor categories on frontend', function () {
    $category1 = ConstructorCategory::factory()->create([
        'name_ru' => 'Базы',
        'sort_order' => 1,
    ]);
    $category2 = ConstructorCategory::factory()->create([
        'name_ru' => 'Протеины',
        'sort_order' => 2,
    ]);

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('Базы');
    $response->assertSee('Протеины');
});

it('displays constructor products in modal', function () {
    $category = ConstructorCategory::factory()->create([
        'name_ru' => 'Базы',
    ]);

    $product1 = ConstructorProduct::factory()->create([
        'constructor_category_id' => $category->id,
        'name_ru' => 'Рис белый',
        'price' => 5.00,
    ]);

    $product2 = ConstructorProduct::factory()->create([
        'constructor_category_id' => $category->id,
        'name_ru' => 'Киноа',
        'price' => 7.00,
    ]);

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('Рис белый');
    $response->assertSee('Киноа');
    $response->assertSee('5.00');
    $response->assertSee('7.00');
});

it('shows empty state when no constructor categories exist', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee(__('frontend.constructor_unavailable'));
});

it('shows click to select text when no products selected in category', function () {
    $category = ConstructorCategory::factory()->create([
        'name_ru' => 'Базы',
    ]);

    ConstructorProduct::factory()->create([
        'constructor_category_id' => $category->id,
        'name_ru' => 'Рис белый',
    ]);

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee(__('frontend.click_to_select'));
});

it('displays all constructor categories with proper ordering', function () {
    $category1 = ConstructorCategory::factory()->create([
        'name_ru' => 'Категория 3',
        'sort_order' => 3,
    ]);
    $category2 = ConstructorCategory::factory()->create([
        'name_ru' => 'Категория 1',
        'sort_order' => 1,
    ]);
    $category3 = ConstructorCategory::factory()->create([
        'name_ru' => 'Категория 2',
        'sort_order' => 2,
    ]);

    $response = $this->get('/');

    $response->assertSuccessful();
    
    $content = $response->getContent();
    $pos1 = strpos($content, 'Категория 1');
    $pos2 = strpos($content, 'Категория 2');
    $pos3 = strpos($content, 'Категория 3');

    expect($pos1)->toBeLessThan($pos2);
    expect($pos2)->toBeLessThan($pos3);
});

it('displays products with nutritional information', function () {
    $category = ConstructorCategory::factory()->create([
        'name_ru' => 'Протеины',
    ]);

    $product = ConstructorProduct::factory()->create([
        'constructor_category_id' => $category->id,
        'name_ru' => 'Курица',
        'price' => 10.00,
        'calories' => 165,
        'proteins' => 31.0,
        'fats' => 3.6,
        'carbohydrates' => 0,
    ]);

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('Курица');
    $response->assertSee('165');
});
