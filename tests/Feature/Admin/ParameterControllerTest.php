<?php

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

it('displays parameters index page for admin', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.parameters.index'));

    $response->assertSuccessful();
    $response->assertViewIs('admin.parameters.index');
    $response->assertViewHas('title', 'Параметры');
    $response->assertViewHas('ordersEnabled');
});

it('can update parameters and enable orders', function () {
    Setting::set('orders_enabled', false);

    $response = $this->actingAs($this->admin)->put(route('admin.parameters.update'), [
        'orders_enabled' => '1',
    ]);

    $response->assertRedirect(route('admin.parameters.index'));
    $response->assertSessionHas('success', 'Параметры сохранены.');
    expect(Setting::get('orders_enabled'))->toBeTrue();
});

it('can update parameters and disable orders', function () {
    Setting::set('orders_enabled', true);

    $response = $this->actingAs($this->admin)->put(route('admin.parameters.update'), [
        'orders_enabled' => '0',
    ]);

    $response->assertRedirect(route('admin.parameters.index'));
    $response->assertSessionHas('success', 'Параметры сохранены.');
    expect(Setting::get('orders_enabled'))->toBeFalse();
});

it('forbids non-admin from accessing parameters index', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('admin.parameters.index'));

    $response->assertForbidden();
});

it('forbids non-admin from updating parameters', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->put(route('admin.parameters.update'), [
        'orders_enabled' => '1',
    ]);

    $response->assertForbidden();
});
