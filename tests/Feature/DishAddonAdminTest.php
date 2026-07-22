<?php

use App\Models\Dish;
use App\Models\DishAddon;
use App\Models\DishCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a dish addon in admin', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.dish-addons.store'), [
        'name_ru' => 'Креветки',
        'price' => 5.50,
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $response->assertRedirect(route('admin.dish-addons.index'));

    $this->assertDatabaseHas('dish_addons', [
        'name_ru' => 'Креветки',
        'price' => 5.50,
        'is_active' => true,
    ]);
});

it('attaches addons to a dish with poster modification id', function () {
    $admin = User::factory()->admin()->create();
    $category = DishCategory::factory()->create();
    $addon = DishAddon::factory()->create(['name_ru' => 'Хлеб', 'price' => 2]);
    $dish = Dish::factory()->create([
        'dish_category_id' => $category->id,
        'name_ru' => 'Суп дня',
    ]);

    $response = $this->actingAs($admin)->put(route('admin.dishes.update', $dish), [
        'name_ru' => 'Суп дня',
        'price' => $dish->price,
        'dish_category_id' => $category->id,
        'sort_order' => 0,
        'addon_ids' => [$addon->id],
        'addon_poster_ids' => [$addon->id => 901],
        'addon_prices' => [$addon->id => 3.00],
    ]);

    $response->assertRedirect(route('admin.dishes.index'));

    expect($dish->fresh()->addons)->toHaveCount(1)
        ->and($dish->fresh()->addons->first()->pivot->poster_modification_id)->toBe(901)
        ->and((float) $dish->fresh()->addons->first()->pivot->price)->toBe(3.0);
});
