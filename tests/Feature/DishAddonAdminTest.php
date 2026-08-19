<?php

use App\Models\Dish;
use App\Models\DishAddon;
use App\Models\DishCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows nutrition fields on the addon create form', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dish-addons.create'))
        ->assertSuccessful()
        ->assertSee('Калории (ккал)')
        ->assertSee('Белки (г)')
        ->assertSee('Жиры (г)')
        ->assertSee('Углеводы (г)');
});

it('creates a dish addon in admin', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.dish-addons.store'), [
        'name_ru' => 'Креветки',
        'price' => 5.50,
        'calories' => 87,
        'proteins' => 12.50,
        'fats' => 3.10,
        'carbohydrates' => 1.40,
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $response->assertRedirect(route('admin.dish-addons.index'));

    $this->assertDatabaseHas('dish_addons', [
        'name_ru' => 'Креветки',
        'price' => 5.50,
        'calories' => 87,
        'proteins' => 12.50,
        'fats' => 3.10,
        'carbohydrates' => 1.40,
        'is_active' => true,
    ]);
});

it('updates dish addon nutrition in admin', function () {
    $admin = User::factory()->admin()->create();
    $addon = DishAddon::factory()->create([
        'name_ru' => 'Хлеб',
        'calories' => 10,
        'proteins' => 1,
        'fats' => 0.5,
        'carbohydrates' => 2,
    ]);

    $response = $this->actingAs($admin)->put(route('admin.dish-addons.update', $addon), [
        'name_ru' => 'Хлеб',
        'price' => $addon->price,
        'calories' => 120,
        'proteins' => 4.20,
        'fats' => 1.10,
        'carbohydrates' => 22.00,
        'sort_order' => $addon->sort_order,
        'is_active' => true,
    ]);

    $response->assertRedirect(route('admin.dish-addons.index'));

    $this->assertDatabaseHas('dish_addons', [
        'id' => $addon->id,
        'calories' => 120,
        'proteins' => 4.20,
        'fats' => 1.10,
        'carbohydrates' => 22.00,
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
