<?php

use App\Models\DishCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('displays categories index page', function () {
    $categories = DishCategory::factory()->count(3)->create();

    $response = $this->actingAs($this->user)->get(route('admin.categories.index'));

    $response->assertSuccessful();
    $response->assertViewIs('admin.categories.index');
    $response->assertViewHas('categories');
});

it('displays create category form', function () {
    $response = $this->actingAs($this->user)->get(route('admin.categories.create'));

    $response->assertSuccessful();
    $response->assertViewIs('admin.categories.create');
});

it('can create a new category', function () {
    $data = [
        'name' => 'Горячие блюда',
        'description' => 'Описание категории',
        'is_active' => true,
        'sort' => 10,
    ];

    $response = $this->actingAs($this->user)->post(route('admin.categories.store'), $data);

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHas('success', 'Категория успешно создана.');

    $this->assertDatabaseHas('dish_categories', [
        'name' => 'Горячие блюда',
        'description' => 'Описание категории',
        'is_active' => true,
        'sort' => 10,
    ]);
});

it('automatically generates slug when creating category', function () {
    $data = [
        'name' => 'Супы и салаты',
        'is_active' => true,
        'sort' => 0,
    ];

    $this->actingAs($this->user)->post(route('admin.categories.store'), $data);

    $category = DishCategory::where('name', 'Супы и салаты')->first();
    expect($category->slug)->not->toBeEmpty();
});

it('can upload image when creating category', function () {
    Storage::fake('public');

    $image = UploadedFile::fake()->image('category.jpg');

    $data = [
        'name' => 'Десерты',
        'image' => $image,
        'is_active' => true,
        'sort' => 0,
    ];

    $response = $this->actingAs($this->user)->post(route('admin.categories.store'), $data);

    $response->assertRedirect(route('admin.categories.index'));

    $category = DishCategory::where('name', 'Десерты')->first();
    expect($category->image)->not->toBeNull();
    Storage::disk('public')->assertExists($category->image);
});

it('validates required fields when creating category', function () {
    $response = $this->actingAs($this->user)->post(route('admin.categories.store'), []);

    $response->assertSessionHasErrors(['name']);
});

it('validates image size when creating category', function () {
    Storage::fake('public');

    $largeImage = UploadedFile::fake()->create('large.jpg', 3000);

    $data = [
        'name' => 'Категория',
        'image' => $largeImage,
    ];

    $response = $this->actingAs($this->user)->post(route('admin.categories.store'), $data);

    $response->assertSessionHasErrors(['image']);
});

it('displays edit category form', function () {
    $category = DishCategory::factory()->create();

    $response = $this->actingAs($this->user)->get(route('admin.categories.edit', $category));

    $response->assertSuccessful();
    $response->assertViewIs('admin.categories.edit');
    $response->assertViewHas('category', $category);
});

it('can update category', function () {
    $category = DishCategory::factory()->create([
        'name' => 'Старое название',
    ]);

    $data = [
        'name' => 'Новое название',
        'description' => 'Новое описание',
        'is_active' => 0,
        'sort' => 20,
    ];

    $response = $this->actingAs($this->user)->put(route('admin.categories.update', $category), $data);

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHas('success', 'Категория успешно обновлена.');

    $category->refresh();
    expect($category->name)->toBe('Новое название');
    expect($category->description)->toBe('Новое описание');
    expect($category->is_active)->toBe(0);
    expect($category->sort)->toBe(20);
});

it('can update category image', function () {
    Storage::fake('public');

    $oldImage = UploadedFile::fake()->image('old.jpg');
    $category = DishCategory::factory()->create();
    $category->update(['image' => $oldImage->store('categories', 'public')]);

    $newImage = UploadedFile::fake()->image('new.jpg');

    $data = [
        'name' => $category->name,
        'image' => $newImage,
        'is_active' => true,
        'sort' => 0,
    ];

    $this->actingAs($this->user)->put(route('admin.categories.update', $category), $data);

    $category->refresh();
    Storage::disk('public')->assertMissing('categories/old.jpg');
    Storage::disk('public')->assertExists($category->image);
});

it('validates unique slug when updating category', function () {
    $category1 = DishCategory::factory()->create(['slug' => 'category-1']);
    $category2 = DishCategory::factory()->create(['slug' => 'category-2']);

    $data = [
        'name' => 'Test',
        'slug' => 'category-1',
        'is_active' => true,
        'sort' => 0,
    ];

    $response = $this->actingAs($this->user)->put(route('admin.categories.update', $category2), $data);

    $response->assertSessionHasErrors(['slug']);
});

it('can delete category', function () {
    $category = DishCategory::factory()->create();

    $response = $this->actingAs($this->user)->delete(route('admin.categories.destroy', $category));

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertSessionHas('success', 'Категория успешно удалена.');

    $this->assertDatabaseMissing('dish_categories', [
        'id' => $category->id,
    ]);
});

it('deletes category image when deleting category', function () {
    Storage::fake('public');

    $image = UploadedFile::fake()->image('category.jpg');
    $category = DishCategory::factory()->create();
    $category->update(['image' => $image->store('categories', 'public')]);

    $imagePath = $category->image;

    $this->actingAs($this->user)->delete(route('admin.categories.destroy', $category));

    Storage::disk('public')->assertMissing($imagePath);
});

it('requires authentication to access categories', function () {
    $response = $this->get(route('admin.categories.index'));
    $response->assertRedirect(route('login'));
});

it('paginates categories on index page', function () {
    DishCategory::factory()->count(20)->create();

    $response = $this->actingAs($this->user)->get(route('admin.categories.index'));

    $response->assertSuccessful();
    $categories = $response->viewData('categories');
    expect($categories)->toHaveCount(15);
});
