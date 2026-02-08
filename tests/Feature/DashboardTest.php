<?php

use App\Enums\OrderStatus;
use App\Models\DishCategory;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $this->get('/admin')->assertRedirect('/login');
});

test('authenticated admin users can visit the dashboard', function () {
    $this->actingAs($user = User::factory()->admin()->create());

    $this->get('/admin')->assertStatus(200);
});

test('authenticated regular users cannot visit the dashboard', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/admin')->assertForbidden();
});

test('old dashboard route redirects to admin dashboard for admins', function () {
    $this->actingAs($user = User::factory()->admin()->create());

    $this->get('/dashboard')->assertRedirect('/admin');
});

test('old dashboard route is forbidden for regular users', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/dashboard')->assertForbidden();
});

test('dashboard displays stats from models', function () {
    $admin = User::factory()->admin()->create();
    DishCategory::factory()->count(2)->create();

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertStatus(200);
    $response->assertViewHas('stats');
    $response->assertViewHas('recentOrders');

    $stats = $response->viewData('stats');
    expect($stats)->toHaveKeys([
        'users_count', 'orders_count', 'orders_new_count', 'orders_in_progress_count',
        'orders_completed_count', 'revenue', 'dishes_count', 'dish_categories_count',
        'drinks_count', 'constructor_categories_count', 'constructor_products_count',
    ]);
    expect($stats['users_count'])->toBe(1);
    expect($stats['dish_categories_count'])->toBe(2);
});

test('dashboard displays recent orders', function () {
    $admin = User::factory()->admin()->create();
    Order::create([
        'customer_name' => 'Иван Тестов',
        'customer_phone' => '+995555123456',
        'delivery_type' => 'pickup',
        'subtotal' => 25.00,
        'delivery_fee' => 0,
        'total' => 25.00,
        'status' => OrderStatus::New,
        'phone_verified' => true,
    ]);

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertStatus(200);
    $response->assertSee('Иван Тестов');
    $response->assertSee('25');
    $response->assertSee('Новый');
});
