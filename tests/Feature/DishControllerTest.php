<?php

use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->category = DishCategory::factory()->create();
});

it('displays dishes index page', function () {
    $dishes = Dish::factory()->count(3)->create(['dish_category_id' => $this->category->id]);

    $response = $this->actingAs($this->user)->get(route('admin.dishes.index'));

    $response->assertSuccessful();
    $response->assertViewIs('admin.dishes.index');
    $response->assertViewHas('dishes');
});

it('displays create dish form', function () {
    $response = $this->actingAs($this->user)->get(route('admin.dishes.create'));

    $response->assertSuccessful();
    $response->assertViewIs('admin.dishes.create');
    $response->assertViewHas('categories');
});

it('can create a new dish', function () {
    $data = [
        'name' => 'Борщ украинский',
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

    $response->assertSessionHasErrors(['name', 'price', 'dish_category_id']);
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
