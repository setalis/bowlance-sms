<?php

use App\Enums\ConstructorType;
use App\Models\ConstructorCategory;
use App\Models\ConstructorProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('filters constructor products by search term in admin', function () {
    $admin = User::factory()->admin()->create();
    $category = ConstructorCategory::factory()->create(['type' => ConstructorType::Bowl]);

    ConstructorProduct::factory()->forCategories($category)->create(['name_ru' => 'Авокадо']);
    ConstructorProduct::factory()->forCategories($category)->create(['name_ru' => 'Лосось']);

    $response = $this->actingAs($admin)->get(route('admin.constructor-products.index', [
        'search' => 'Авокадо',
    ]));

    $response->assertSuccessful();
    $response->assertSee('Авокадо');
    $response->assertDontSee('Лосось');
});

it('filters constructor products by category in admin', function () {
    $admin = User::factory()->admin()->create();

    $bowlCategory = ConstructorCategory::factory()->create([
        'name_ru' => 'Базы боула',
        'type' => ConstructorType::Bowl,
    ]);
    $breakfastCategory = ConstructorCategory::factory()->create([
        'name_ru' => 'Базы завтрака',
        'type' => ConstructorType::Breakfast,
    ]);

    ConstructorProduct::factory()->forCategories($bowlCategory)->create(['name_ru' => 'Рис']);
    ConstructorProduct::factory()->forCategories($breakfastCategory)->create(['name_ru' => 'Яйцо']);
    ConstructorProduct::factory()
        ->forCategories($bowlCategory, $breakfastCategory)
        ->create(['name_ru' => 'Авокадо']);

    $response = $this->actingAs($admin)->get(route('admin.constructor-products.index', [
        'category_id' => $breakfastCategory->id,
    ]));

    $response->assertSuccessful();
    $response->assertSee('Яйцо');
    $response->assertSee('Авокадо');
    $response->assertDontSee('Рис');
});

it('shows empty state when filters match nothing', function () {
    $admin = User::factory()->admin()->create();
    $category = ConstructorCategory::factory()->create(['type' => ConstructorType::Bowl]);

    ConstructorProduct::factory()->forCategories($category)->create(['name_ru' => 'Лосось']);

    $response = $this->actingAs($admin)->get(route('admin.constructor-products.index', [
        'search' => 'несуществующий-продукт',
    ]));

    $response->assertSuccessful();
    $response->assertSee('Ничего не найдено');
    $response->assertDontSee('Лосось');
});

it('shows search form and category select on admin index', function () {
    $admin = User::factory()->admin()->create();

    ConstructorCategory::factory()->create([
        'name_ru' => 'Протеины',
        'type' => ConstructorType::Bowl,
    ]);

    $response = $this->actingAs($admin)->get(route('admin.constructor-products.index'));

    $response->assertSuccessful();
    $response->assertSee('name="search"', false);
    $response->assertSee('name="category_id"', false);
    $response->assertSee('Все категории');
    $response->assertSee('Протеины');
});
