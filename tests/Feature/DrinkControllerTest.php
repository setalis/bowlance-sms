<?php

use App\Models\Drink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
});

it('displays drinks index page', function () {
    $drinks = Drink::factory()->count(3)->create();

    $response = $this->actingAs($this->user)->get(route('admin.drinks.index'));

    $response->assertSuccessful();
    $response->assertViewIs('admin.drinks.index');
    $response->assertViewHas('drinks');
});

it('displays create drink form', function () {
    $response = $this->actingAs($this->user)->get(route('admin.drinks.create'));

    $response->assertSuccessful();
    $response->assertViewIs('admin.drinks.create');
});

it('can create a new drink', function () {
    $data = [
        'name' => 'Coca-Cola',
        'name_ru' => 'Кока-Кола',
        'description' => 'Классический газированный напиток',
        'description_ru' => 'Классический газированный напиток',
        'price' => 150.00,
        'volume' => '330 мл',
        'calories' => 140,
        'proteins' => 0,
        'fats' => 0,
        'carbohydrates' => 35,
        'fiber' => 0,
        'sort_order' => 10,
    ];

    $response = $this->actingAs($this->user)->post(route('admin.drinks.store'), $data);

    $response->assertRedirect(route('admin.drinks.index'));
    $response->assertSessionHas('success', 'Напиток успешно создан.');

    $this->assertDatabaseHas('drinks', [
        'name' => 'Coca-Cola',
        'name_ru' => 'Кока-Кола',
        'description' => 'Классический газированный напиток',
        'price' => 150.00,
    ]);
});

it('can upload image when creating drink', function () {
    Storage::fake('public');

    $image = UploadedFile::fake()->image('drink.jpg');

    $data = [
        'name' => 'Pepsi',
        'name_ru' => 'Пепси',
        'price' => 140.00,
        'image' => $image,
        'sort_order' => 0,
    ];

    $response = $this->actingAs($this->user)->post(route('admin.drinks.store'), $data);

    $response->assertRedirect(route('admin.drinks.index'));

    $drink = Drink::where('name', 'Pepsi')->first();
    expect($drink->image)->not->toBeNull();
    Storage::disk('public')->assertExists($drink->image);
});

it('validates required fields when creating drink', function () {
    $response = $this->actingAs($this->user)->post(route('admin.drinks.store'), []);

    $response->assertSessionHasErrors(['name_ru', 'price']);
});

it('validates price is numeric when creating drink', function () {
    $data = [
        'name_ru' => 'Тестовый напиток',
        'price' => 'not-a-number',
    ];

    $response = $this->actingAs($this->user)->post(route('admin.drinks.store'), $data);

    $response->assertSessionHasErrors(['price']);
});

it('validates discount price is less than price', function () {
    $data = [
        'name_ru' => 'Тестовый напиток',
        'price' => 100.00,
        'discount_price' => 150.00,
    ];

    $response = $this->actingAs($this->user)->post(route('admin.drinks.store'), $data);

    $response->assertSessionHasErrors(['discount_price']);
});

it('validates image size when creating drink', function () {
    Storage::fake('public');

    $largeImage = UploadedFile::fake()->create('large.jpg', 3000);

    $data = [
        'name_ru' => 'Напиток',
        'price' => 100.00,
        'image' => $largeImage,
    ];

    $response = $this->actingAs($this->user)->post(route('admin.drinks.store'), $data);

    $response->assertSessionHasErrors(['image']);
});

it('displays edit drink form', function () {
    $drink = Drink::factory()->create();

    $response = $this->actingAs($this->user)->get(route('admin.drinks.edit', $drink));

    $response->assertSuccessful();
    $response->assertViewIs('admin.drinks.edit');
    $response->assertViewHas('drink', $drink);
});

it('can update drink', function () {
    $drink = Drink::factory()->withoutDiscount()->create([
        'name' => 'Старое название',
        'name_ru' => 'Старое название',
        'price' => 100.00,
    ]);

    $data = [
        'name' => 'Новое название',
        'name_ru' => 'Новое название',
        'description' => 'Новое описание',
        'description_ru' => 'Новое описание',
        'price' => 200.00,
        'volume' => '500 мл',
        'calories' => 200,
        'sort_order' => 20,
    ];

    $response = $this->actingAs($this->user)->put(route('admin.drinks.update', $drink), $data);

    $response->assertRedirect(route('admin.drinks.index'));
    $response->assertSessionHas('success', 'Напиток успешно обновлен.');

    $drink->refresh();
    expect($drink->name_ru)->toBe('Новое название');
    expect($drink->description_ru)->toBe('Новое описание');
    expect($drink->price)->toBe('200.00');
    expect($drink->volume)->toBe('500 мл');
    expect($drink->calories)->toBe(200);
    expect($drink->sort_order)->toBe(20);
});

it('can update drink image', function () {
    Storage::fake('public');

    $oldImage = UploadedFile::fake()->image('old.jpg');
    $drink = Drink::factory()->create();
    $drink->update(['image' => $oldImage->store('drinks', 'public')]);

    $newImage = UploadedFile::fake()->image('new.jpg');

    $data = [
        'name' => $drink->name,
        'name_ru' => $drink->name_ru,
        'price' => $drink->price,
        'image' => $newImage,
        'sort_order' => 0,
    ];

    $this->actingAs($this->user)->put(route('admin.drinks.update', $drink), $data);

    $drink->refresh();
    Storage::disk('public')->assertMissing('drinks/old.jpg');
    Storage::disk('public')->assertExists($drink->image);
});

it('can delete drink', function () {
    $drink = Drink::factory()->create();

    $response = $this->actingAs($this->user)->delete(route('admin.drinks.destroy', $drink));

    $response->assertRedirect(route('admin.drinks.index'));
    $response->assertSessionHas('success', 'Напиток успешно удален.');

    $this->assertDatabaseMissing('drinks', [
        'id' => $drink->id,
    ]);
});

it('deletes drink image when deleting drink', function () {
    Storage::fake('public');

    $image = UploadedFile::fake()->image('drink.jpg');
    $drink = Drink::factory()->create();
    $drink->update(['image' => $image->store('drinks', 'public')]);

    $imagePath = $drink->image;

    $this->actingAs($this->user)->delete(route('admin.drinks.destroy', $drink));

    Storage::disk('public')->assertMissing($imagePath);
});

it('requires authentication to access drinks', function () {
    $response = $this->get(route('admin.drinks.index'));
    $response->assertRedirect(route('login'));
});

it('paginates drinks on index page', function () {
    Drink::factory()->count(20)->create();

    $response = $this->actingAs($this->user)->get(route('admin.drinks.index'));

    $response->assertSuccessful();
    $drinks = $response->viewData('drinks');
    expect($drinks)->toHaveCount(15);
});
