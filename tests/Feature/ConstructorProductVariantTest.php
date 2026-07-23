<?php

use App\Enums\ConstructorType;
use App\Models\ConstructorCategory;
use App\Models\ConstructorProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores different price and weight for bowl and breakfast variants of the same product', function () {
    $bowlCategory = ConstructorCategory::factory()->create([
        'type' => ConstructorType::Bowl,
    ]);
    $breakfastCategory = ConstructorCategory::factory()->create([
        'type' => ConstructorType::Breakfast,
    ]);

    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.constructor-products.store'), [
        'name_ru' => 'Лосось',
        'category_ids' => [$bowlCategory->id, $breakfastCategory->id],
        'sort_order' => 1,
        'variants' => [
            'bowl' => [
                'price' => 10,
                'weight_volume' => '100 г',
                'calories' => 200,
                'proteins' => 20,
                'fats' => 10,
                'carbohydrates' => 0,
                'fiber' => 0,
                'poster_modification_id' => 301,
            ],
            'breakfast' => [
                'price' => 5,
                'weight_volume' => '50 г',
                'calories' => 100,
                'proteins' => 10,
                'fats' => 5,
                'carbohydrates' => 0,
                'fiber' => 0,
                'poster_modification_id' => 401,
            ],
        ],
    ]);

    $response->assertRedirect(route('admin.constructor-products.index'));

    $product = ConstructorProduct::query()->where('name_ru', 'Лосось')->first();

    expect($product)->not->toBeNull();

    $bowlVariant = $product->variantFor(ConstructorType::Bowl);
    $breakfastVariant = $product->variantFor(ConstructorType::Breakfast);

    expect($bowlVariant)->not->toBeNull()
        ->and((float) $bowlVariant->price)->toBe(10.0)
        ->and($bowlVariant->weight_volume)->toBe('100 г')
        ->and($bowlVariant->calories)->toBe(200)
        ->and($bowlVariant->poster_modification_id)->toBe(301)
        ->and($breakfastVariant)->not->toBeNull()
        ->and((float) $breakfastVariant->price)->toBe(5.0)
        ->and($breakfastVariant->weight_volume)->toBe('50 г')
        ->and($breakfastVariant->calories)->toBe(100)
        ->and($breakfastVariant->poster_modification_id)->toBe(401);
});

it('renders bowl and breakfast prices for the same product on the homepage', function () {
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
        ->create(['name_ru' => 'Лосось']);

    $product->variants()->updateOrCreate(
        ['type' => ConstructorType::Bowl],
        ['price' => 10, 'weight_volume' => '100 г', 'calories' => 200]
    );
    $product->variants()->updateOrCreate(
        ['type' => ConstructorType::Breakfast],
        ['price' => 5, 'weight_volume' => '50 г', 'calories' => 100]
    );

    $response = $this->get('/');

    $response->assertSuccessful();

    $content = $response->getContent();
    $constructorPos = strpos($content, 'id="constructor-content"');
    $breakfastPos = strpos($content, 'id="breakfast-constructor-content"');

    expect($constructorPos)->not->toBeFalse()
        ->and($breakfastPos)->not->toBeFalse();

    $bowlSection = substr($content, $constructorPos, $breakfastPos - $constructorPos);
    $breakfastSection = substr($content, $breakfastPos);

    expect($bowlSection)->toContain('Лосось')
        ->and($bowlSection)->toContain('10.00')
        ->and($bowlSection)->toContain('100 г')
        ->and($breakfastSection)->toContain('Лосось')
        ->and($breakfastSection)->toContain('5.00')
        ->and($breakfastSection)->toContain('50 г');
});

it('requires variant price for each selected constructor type', function () {
    $admin = User::factory()->admin()->create();

    $bowlCategory = ConstructorCategory::factory()->create([
        'type' => ConstructorType::Bowl,
    ]);
    $breakfastCategory = ConstructorCategory::factory()->create([
        'type' => ConstructorType::Breakfast,
    ]);

    $response = $this->actingAs($admin)
        ->from(route('admin.constructor-products.create'))
        ->post(route('admin.constructor-products.store'), [
            'name_ru' => 'Лосось',
            'category_ids' => [$bowlCategory->id, $breakfastCategory->id],
            'sort_order' => 1,
            'variants' => [
                'bowl' => [
                    'price' => 10,
                ],
            ],
        ]);

    $response->assertRedirect(route('admin.constructor-products.create'));
    $response->assertSessionHasErrors('variants.breakfast.price');
});
