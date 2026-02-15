<?php

use App\Models\Discount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->admin()->create();
});

it('displays discounts index page', function () {
    Discount::factory()->count(2)->create();

    $response = $this->actingAs($this->user)->get(route('admin.discounts.index'));

    $response->assertSuccessful();
    $response->assertViewIs('admin.discounts.index');
    $response->assertViewHas('discounts');
});

it('displays create discount form', function () {
    $response = $this->actingAs($this->user)->get(route('admin.discounts.create'));

    $response->assertSuccessful();
    $response->assertViewIs('admin.discounts.create');
});

it('can create a new discount with percent type', function () {
    $data = [
        'name' => 'Скидка за самовывоз',
        'size' => 10,
        'type' => 'percent',
        'scope' => 'pickup',
        'is_active' => '1',
    ];

    $response = $this->actingAs($this->user)->post(route('admin.discounts.store'), $data);

    $response->assertRedirect(route('admin.discounts.index'));
    $response->assertSessionHas('success', 'Скидка успешно создана.');

    $this->assertDatabaseHas('discounts', [
        'name' => 'Скидка за самовывоз',
        'size' => 10,
        'type' => 'percent',
        'scope' => 'pickup',
        'is_active' => true,
    ]);
});

it('can create a new discount with amount type', function () {
    $data = [
        'size' => 2.5,
        'type' => 'amount',
        'scope' => 'pickup',
        'is_active' => '1',
    ];

    $response = $this->actingAs($this->user)->post(route('admin.discounts.store'), $data);

    $response->assertRedirect(route('admin.discounts.index'));
    $this->assertDatabaseHas('discounts', [
        'size' => 2.5,
        'type' => 'amount',
    ]);
});

it('validates required fields when creating discount', function () {
    $response = $this->actingAs($this->user)->post(route('admin.discounts.store'), [
        'type' => 'percent',
    ]);

    $response->assertSessionHasErrors(['size']);
});

it('displays edit discount form', function () {
    $discount = Discount::factory()->create();

    $response = $this->actingAs($this->user)->get(route('admin.discounts.edit', $discount));

    $response->assertSuccessful();
    $response->assertViewIs('admin.discounts.edit');
    $response->assertViewHas('discount', $discount);
});

it('can update a discount', function () {
    $discount = Discount::factory()->percent()->create(['size' => 10]);

    $data = [
        'name' => 'Обновлённая скидка',
        'size' => 15,
        'type' => 'percent',
        'scope' => 'pickup',
        'is_active' => '1',
    ];

    $response = $this->actingAs($this->user)->put(route('admin.discounts.update', $discount), $data);

    $response->assertRedirect(route('admin.discounts.index'));
    $response->assertSessionHas('success', 'Скидка успешно обновлена.');

    $discount->refresh();
    expect($discount->name)->toBe('Обновлённая скидка');
    expect((float) $discount->size)->toBe(15.0);
});

it('can delete a discount', function () {
    $discount = Discount::factory()->create();

    $response = $this->actingAs($this->user)->delete(route('admin.discounts.destroy', $discount));

    $response->assertRedirect(route('admin.discounts.index'));
    $response->assertSessionHas('success', 'Скидка удалена.');
    $this->assertModelMissing($discount);
});

it('forbids non-admin from accessing discounts index', function () {
    $regularUser = User::factory()->create();

    $response = $this->actingAs($regularUser)->get(route('admin.discounts.index'));

    $response->assertForbidden();
});
