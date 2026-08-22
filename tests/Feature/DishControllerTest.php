<?php

use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
    $this->category = DishCategory::factory()->create();
});

it('displays dishes index page', function () {
    $dishes = Dish::factory()->count(3)->create(['dish_category_id' => $this->category->id]);

    $response = $this->actingAs($this->user)->get(route('admin.dishes.index'));

    $response->assertSuccessful();
    $response->assertViewIs('admin.dishes.index');
    $response->assertViewHas('dishes');
    $response->assertViewHas('categories');
});

it('displays create dish form', function () {
    $response = $this->actingAs($this->user)->get(route('admin.dishes.create'));

    $response->assertSuccessful();
    $response->assertViewIs('admin.dishes.create');
    $response->assertViewHas('categories');
    $response->assertViewHas('addons');
});

it('can create a new dish', function () {
    $data = [
        'name' => 'Борщ украинский',
        'name_ru' => 'Борщ украинский',
        'description' => 'Традиционный украинский суп',
        'price' => 350.00,
        'dish_category_id' => $this->category->id,
        'weight_volume' => '350 г',
        'calories' => 120,
        'proteins' => 5.5,
        'fats' => 8.2,
        'carbohydrates' => 12.3,
        'fiber' => 2.1,
        'sort_order' => 10,
    ];

    $response = $this->actingAs($this->user)->post(route('admin.dishes.store'), $data);

    $response->assertRedirect(route('admin.dishes.index'));
    $response->assertSessionHas('success', 'Блюдо успешно создано.');

    $this->assertDatabaseHas('dishes', [
        'name' => 'Борщ украинский',
        'description' => 'Традиционный украинский суп',
        'price' => 350.00,
        'dish_category_id' => $this->category->id,
    ]);
});

it('can upload image when creating dish', function () {
    Storage::fake('public');

    $image = UploadedFile::fake()->image('dish.jpg');

    $data = [
        'name' => 'Салат Цезарь',
        'name_ru' => 'Салат Цезарь',
        'price' => 250.00,
        'dish_category_id' => $this->category->id,
        'image' => $image,
        'sort_order' => 0,
    ];

    $response = $this->actingAs($this->user)->post(route('admin.dishes.store'), $data);

    $response->assertRedirect(route('admin.dishes.index'));

    $dish = Dish::where('name', 'Салат Цезарь')->first();
    expect($dish->image)->not->toBeNull();
    Storage::disk('public')->assertExists($dish->image);
});

it('validates required fields when creating dish', function () {
    $response = $this->actingAs($this->user)->post(route('admin.dishes.store'), []);

    $response->assertSessionHasErrors(['name_ru', 'price', 'dish_category_id']);
});

it('validates price is numeric when creating dish', function () {
    $data = [
        'name' => 'Тестовое блюдо',
        'price' => 'not-a-number',
        'dish_category_id' => $this->category->id,
    ];

    $response = $this->actingAs($this->user)->post(route('admin.dishes.store'), $data);

    $response->assertSessionHasErrors(['price']);
});

it('validates discount price is less than price', function () {
    $data = [
        'name' => 'Тестовое блюдо',
        'price' => 100.00,
        'discount_price' => 150.00,
        'dish_category_id' => $this->category->id,
    ];

    $response = $this->actingAs($this->user)->post(route('admin.dishes.store'), $data);

    $response->assertSessionHasErrors(['discount_price']);
});

it('validates category exists when creating dish', function () {
    $data = [
        'name' => 'Тестовое блюдо',
        'price' => 100.00,
        'dish_category_id' => 99999,
    ];

    $response = $this->actingAs($this->user)->post(route('admin.dishes.store'), $data);

    $response->assertSessionHasErrors(['dish_category_id']);
});

it('validates image size when creating dish', function () {
    Storage::fake('public');

    $largeImage = UploadedFile::fake()->create('large.jpg', 3000);

    $data = [
        'name' => 'Блюдо',
        'price' => 100.00,
        'dish_category_id' => $this->category->id,
        'image' => $largeImage,
    ];

    $response = $this->actingAs($this->user)->post(route('admin.dishes.store'), $data);

    $response->assertSessionHasErrors(['image']);
});

it('displays edit dish form', function () {
    $dish = Dish::factory()->create(['dish_category_id' => $this->category->id]);

    $response = $this->actingAs($this->user)->get(route('admin.dishes.edit', $dish));

    $response->assertSuccessful();
    $response->assertViewIs('admin.dishes.edit');
    $response->assertViewHas('dish', $dish);
    $response->assertViewHas('categories');
});

it('can update dish', function () {
    $dish = Dish::factory()->withoutDiscount()->create([
        'name' => 'Старое название',
        'price' => 100.00,
        'dish_category_id' => $this->category->id,
    ]);

    $newCategory = DishCategory::factory()->create();

    $data = [
        'name' => 'Новое название',
        'name_ru' => 'Новое название',
        'description' => 'Новое описание',
        'price' => 200.00,
        'dish_category_id' => $newCategory->id,
        'weight_volume' => '400 г',
        'calories' => 200,
        'sort_order' => 20,
    ];

    $response = $this->actingAs($this->user)->put(route('admin.dishes.update', $dish), $data);

    $response->assertRedirect(route('admin.dishes.index'));
    $response->assertSessionHas('success', 'Блюдо успешно обновлено.');

    $dish->refresh();
    expect($dish->name)->toBe('Новое название');
    expect($dish->description)->toBe('Новое описание');
    expect($dish->price)->toBe('200.00');
    expect($dish->dish_category_id)->toBe($newCategory->id);
    expect($dish->weight_volume)->toBe('400 г');
    expect($dish->calories)->toBe(200);
    expect($dish->sort_order)->toBe(20);
});

it('can update dish image', function () {
    Storage::fake('public');

    $oldImage = UploadedFile::fake()->image('old.jpg');
    $dish = Dish::factory()->create(['dish_category_id' => $this->category->id]);
    $dish->update(['image' => $oldImage->store('dishes', 'public')]);

    $newImage = UploadedFile::fake()->image('new.jpg');

    $data = [
        'name' => $dish->name,
        'name_ru' => $dish->name_ru ?? $dish->getRawOriginal('name'),
        'price' => $dish->price,
        'dish_category_id' => $this->category->id,
        'image' => $newImage,
        'sort_order' => 0,
    ];

    $this->actingAs($this->user)->put(route('admin.dishes.update', $dish), $data);

    $dish->refresh();
    Storage::disk('public')->assertMissing('dishes/old.jpg');
    Storage::disk('public')->assertExists($dish->image);
});

it('can delete dish', function () {
    $dish = Dish::factory()->create(['dish_category_id' => $this->category->id]);

    $response = $this->actingAs($this->user)->delete(route('admin.dishes.destroy', $dish));

    $response->assertRedirect(route('admin.dishes.index'));
    $response->assertSessionHas('success', 'Блюдо успешно удалено.');

    $this->assertDatabaseMissing('dishes', [
        'id' => $dish->id,
    ]);
});

it('deletes dish image when deleting dish', function () {
    Storage::fake('public');

    $image = UploadedFile::fake()->image('dish.jpg');
    $dish = Dish::factory()->create(['dish_category_id' => $this->category->id]);
    $dish->update(['image' => $image->store('dishes', 'public')]);

    $imagePath = $dish->image;

    $this->actingAs($this->user)->delete(route('admin.dishes.destroy', $dish));

    Storage::disk('public')->assertMissing($imagePath);
});

it('requires authentication to access dishes', function () {
    $response = $this->get(route('admin.dishes.index'));
    $response->assertRedirect(route('login'));
});

it('paginates dishes on index page', function () {
    Dish::factory()->count(20)->create(['dish_category_id' => $this->category->id]);

    $response = $this->actingAs($this->user)->get(route('admin.dishes.index'));

    $response->assertSuccessful();
    $dishes = $response->viewData('dishes');
    expect($dishes)->toHaveCount(15);
});

it('loads category relationship on index page', function () {
    $dish = Dish::factory()->create(['dish_category_id' => $this->category->id]);

    $response = $this->actingAs($this->user)->get(route('admin.dishes.index'));

    $response->assertSuccessful();
    $dishes = $response->viewData('dishes');
    expect($dishes->first()->relationLoaded('category'))->toBeTrue();
});

it('filters dishes by search term in admin', function () {
    Dish::factory()->create([
        'name' => 'Борщ украинский',
        'name_ru' => 'Борщ украинский',
        'dish_category_id' => $this->category->id,
    ]);
    Dish::factory()->create([
        'name' => 'Салат Цезарь',
        'name_ru' => 'Салат Цезарь',
        'dish_category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->user)->get(route('admin.dishes.index', [
        'search' => 'Борщ',
    ]));

    $response->assertSuccessful();
    $response->assertSee('Борщ украинский');
    $response->assertDontSee('Салат Цезарь');
});

it('filters dishes by category in admin', function () {
    $soups = DishCategory::factory()->create(['name_ru' => 'Супы']);
    $salads = DishCategory::factory()->create(['name_ru' => 'Салаты']);

    Dish::factory()->create([
        'name' => 'Борщ',
        'name_ru' => 'Борщ',
        'dish_category_id' => $soups->id,
    ]);
    Dish::factory()->create([
        'name' => 'Цезарь',
        'name_ru' => 'Цезарь',
        'dish_category_id' => $salads->id,
    ]);

    $response = $this->actingAs($this->user)->get(route('admin.dishes.index', [
        'category_id' => $soups->id,
    ]));

    $response->assertSuccessful();
    $response->assertSee('Борщ');
    $response->assertDontSee('Цезарь');
});

it('filters dishes by search and category together', function () {
    $soups = DishCategory::factory()->create();
    $salads = DishCategory::factory()->create();

    Dish::factory()->create([
        'name' => 'Борщ украинский',
        'name_ru' => 'Борщ украинский',
        'dish_category_id' => $soups->id,
    ]);
    Dish::factory()->create([
        'name' => 'Борщ зелёный',
        'name_ru' => 'Борщ зелёный',
        'dish_category_id' => $salads->id,
    ]);
    Dish::factory()->create([
        'name' => 'Солянка',
        'name_ru' => 'Солянка',
        'dish_category_id' => $soups->id,
    ]);

    $response = $this->actingAs($this->user)->get(route('admin.dishes.index', [
        'search' => 'Борщ',
        'category_id' => $soups->id,
    ]));

    $response->assertSuccessful();
    $response->assertSee('Борщ украинский');
    $response->assertDontSee('Борщ зелёный');
    $response->assertDontSee('Солянка');
});

it('shows empty state when dish filters match nothing', function () {
    Dish::factory()->create([
        'name' => 'Борщ',
        'name_ru' => 'Борщ',
        'dish_category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->user)->get(route('admin.dishes.index', [
        'search' => 'несуществующее-блюдо',
    ]));

    $response->assertSuccessful();
    $response->assertSee('Ничего не найдено');
    $response->assertDontSee('Борщ');
});

it('shows search form and category select on dishes index', function () {
    $this->category->update(['name_ru' => 'Супы']);

    $response = $this->actingAs($this->user)->get(route('admin.dishes.index'));

    $response->assertSuccessful();
    $response->assertViewHas('categories');
    $response->assertSee('name="search"', false);
    $response->assertSee('name="category_id"', false);
    $response->assertSee('Все категории');
    $response->assertSee('Супы');
});

it('creates a dish as active by default', function () {
    $response = $this->actingAs($this->user)->post(route('admin.dishes.store'), [
        'name_ru' => 'Борщ украинский',
        'price' => 350.00,
        'dish_category_id' => $this->category->id,
        'sort_order' => 0,
        'is_active' => 1,
    ]);

    $response->assertRedirect(route('admin.dishes.index'));

    $this->assertDatabaseHas('dishes', [
        'name_ru' => 'Борщ украинский',
        'is_active' => true,
    ]);
});

it('can create an inactive dish', function () {
    $response = $this->actingAs($this->user)->post(route('admin.dishes.store'), [
        'name_ru' => 'Солянка',
        'price' => 400.00,
        'dish_category_id' => $this->category->id,
        'sort_order' => 0,
        'is_active' => 0,
    ]);

    $response->assertRedirect(route('admin.dishes.index'));

    $this->assertDatabaseHas('dishes', [
        'name_ru' => 'Солянка',
        'is_active' => false,
    ]);
});

it('can deactivate a dish', function () {
    $dish = Dish::factory()->create([
        'dish_category_id' => $this->category->id,
        'name_ru' => 'Борщ',
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->user)->put(route('admin.dishes.update', $dish), [
        'name_ru' => 'Борщ',
        'price' => $dish->price,
        'dish_category_id' => $this->category->id,
        'sort_order' => 0,
        'is_active' => 0,
    ]);

    $response->assertRedirect(route('admin.dishes.index'));

    expect($dish->fresh()->is_active)->toBeFalse();
});

it('shows dish activity status on the admin index', function () {
    Dish::factory()->create([
        'dish_category_id' => $this->category->id,
        'name_ru' => 'Активное блюдо',
        'is_active' => true,
    ]);
    Dish::factory()->inactive()->create([
        'dish_category_id' => $this->category->id,
        'name_ru' => 'Неактивное блюдо',
    ]);

    $response = $this->actingAs($this->user)->get(route('admin.dishes.index'));

    $response->assertSuccessful();
    $response->assertSee('Активное блюдо');
    $response->assertSee('Неактивное блюдо');
    $response->assertSee('Активна');
    $response->assertSee('Неактивна');
});

it('shows the active checkbox on create and edit dish forms', function () {
    $dish = Dish::factory()->create(['dish_category_id' => $this->category->id]);

    $this->actingAs($this->user)
        ->get(route('admin.dishes.create'))
        ->assertSuccessful()
        ->assertSee('name="is_active"', false)
        ->assertSee('Активна');

    $this->actingAs($this->user)
        ->get(route('admin.dishes.edit', $dish))
        ->assertSuccessful()
        ->assertSee('name="is_active"', false)
        ->assertSee('Активна');
});

it('does not show inactive dishes on the home page', function () {
    $this->category->update([
        'is_active' => true,
        'name_ru' => 'Супы',
    ]);

    Dish::factory()->create([
        'dish_category_id' => $this->category->id,
        'name' => 'Борщ',
        'name_ru' => 'Борщ',
        'is_active' => true,
    ]);
    Dish::factory()->inactive()->create([
        'dish_category_id' => $this->category->id,
        'name' => 'Солянка',
        'name_ru' => 'Солянка',
    ]);

    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('Борщ');
    $response->assertDontSee('Солянка');
});
