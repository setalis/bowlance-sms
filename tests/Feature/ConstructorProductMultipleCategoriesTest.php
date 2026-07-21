<?php

use App\Enums\ConstructorType;
use App\Models\ConstructorCategory;
use App\Models\ConstructorProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows a product to belong to bowl and breakfast categories', function () {
    $bowlCategory = ConstructorCategory::factory()->create([
        'name_ru' => 'Базы боула',
        'type' => ConstructorType::Bowl,
    ]);

    $breakfastCategory = ConstructorCategory::factory()->create([
        'name_ru' => 'Базы завтрака',
        'type' => ConstructorType::Breakfast,
    ]);

    $product = ConstructorProduct::factory()
        ->forCategories($bowlCategory, $breakfastCategory)
        ->create(['name_ru' => 'Авокадо']);

    expect($product->categories)->toHaveCount(2)
        ->and($product->categories->pluck('id')->all())
        ->toContain($bowlCategory->id, $breakfastCategory->id);

    $response = $this->get('/');

    $response->assertSuccessful();

    $content = $response->getContent();
    $constructorPos = strpos($content, 'id="constructor-content"');
    $breakfastPos = strpos($content, 'id="breakfast-constructor-content"');

    expect($constructorPos)->not->toBeFalse()
        ->and($breakfastPos)->not->toBeFalse();

    $bowlSection = substr($content, $constructorPos, $breakfastPos - $constructorPos);
    $breakfastSection = substr($content, $breakfastPos);

    expect($bowlSection)->toContain('Авокадо')
        ->and($breakfastSection)->toContain('Авокадо');
});

it('creates a constructor product with multiple categories in admin', function () {
    $admin = User::factory()->admin()->create();

    $bowlCategory = ConstructorCategory::factory()->create([
        'type' => ConstructorType::Bowl,
    ]);
    $breakfastCategory = ConstructorCategory::factory()->create([
        'type' => ConstructorType::Breakfast,
    ]);

    $response = $this->actingAs($admin)->post(route('admin.constructor-products.store'), [
        'name_ru' => 'Лосось',
        'price' => 12.50,
        'category_ids' => [$bowlCategory->id, $breakfastCategory->id],
        'sort_order' => 1,
    ]);

    $response->assertRedirect(route('admin.constructor-products.index'));

    $product = ConstructorProduct::query()->where('name_ru', 'Лосось')->first();

    expect($product)->not->toBeNull()
        ->and($product->categories()->pluck('constructor_categories.id')->all())
        ->toEqualCanonicalizing([$bowlCategory->id, $breakfastCategory->id]);
});

it('updates constructor product categories in admin', function () {
    $admin = User::factory()->admin()->create();

    $bowlCategory = ConstructorCategory::factory()->create([
        'type' => ConstructorType::Bowl,
    ]);
    $breakfastCategory = ConstructorCategory::factory()->create([
        'type' => ConstructorType::Breakfast,
    ]);
    $anotherBowlCategory = ConstructorCategory::factory()->create([
        'type' => ConstructorType::Bowl,
    ]);

    $product = ConstructorProduct::factory()
        ->forCategories($bowlCategory)
        ->create(['name_ru' => 'Тофу']);

    $response = $this->actingAs($admin)->put(route('admin.constructor-products.update', $product), [
        'name_ru' => 'Тофу',
        'price' => $product->price,
        'category_ids' => [$breakfastCategory->id, $anotherBowlCategory->id],
        'sort_order' => $product->sort_order,
    ]);

    $response->assertRedirect(route('admin.constructor-products.index'));

    expect($product->fresh()->categories()->pluck('constructor_categories.id')->all())
        ->toEqualCanonicalizing([$breakfastCategory->id, $anotherBowlCategory->id]);
});

it('requires at least one category when creating a product', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->from(route('admin.constructor-products.create'))
        ->post(route('admin.constructor-products.store'), [
            'name_ru' => 'Без категории',
            'price' => 5,
            'sort_order' => 0,
        ]);

    $response->assertRedirect(route('admin.constructor-products.create'));
    $response->assertSessionHasErrors('category_ids');
});
