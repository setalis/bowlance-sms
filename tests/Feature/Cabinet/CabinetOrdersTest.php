<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

test('cabinet orders index is displayed', function () {
    $user = User::factory()->create(['phone' => '+995555111555']);

    $this->actingAs($user)
        ->get(route('cabinet.orders.index'))
        ->assertOk();
});

test('cabinet shows only own orders', function () {
    $user = User::factory()->create(['phone' => '+995555222666']);
    $other = User::factory()->create(['phone' => '+995555333777']);

    $own = Order::create([
        'user_id' => $user->id,
        'customer_name' => $user->name,
        'customer_phone' => $user->phone,
        'subtotal' => 10,
        'delivery_fee' => 0,
        'total' => 10,
        'status' => OrderStatus::New,
    ]);
    $foreign = Order::create([
        'user_id' => $other->id,
        'customer_name' => $other->name,
        'customer_phone' => $other->phone,
        'subtotal' => 20,
        'delivery_fee' => 0,
        'total' => 20,
        'status' => OrderStatus::New,
    ]);

    $response = $this->actingAs($user)->get(route('cabinet.orders.index'));

    $response->assertOk();
    $response->assertSee($own->order_number);
    $response->assertDontSee($foreign->order_number);
});

test('cabinet user can view own order', function () {
    $user = User::factory()->create(['phone' => '+995555444888']);
    $order = Order::create([
        'user_id' => $user->id,
        'customer_name' => $user->name,
        'customer_phone' => $user->phone,
        'subtotal' => 15,
        'delivery_fee' => 0,
        'total' => 15,
        'status' => OrderStatus::New,
    ]);

    $this->actingAs($user)
        ->get(route('cabinet.orders.show', $order))
        ->assertOk()
        ->assertSee($order->order_number);
});

test('cabinet user cannot view another user order', function () {
    $user = User::factory()->create(['phone' => '+995555555999']);
    $other = User::factory()->create(['phone' => '+995555666000']);
    $order = Order::create([
        'user_id' => $other->id,
        'customer_name' => $other->name,
        'customer_phone' => $other->phone,
        'subtotal' => 25,
        'delivery_fee' => 0,
        'total' => 25,
        'status' => OrderStatus::New,
    ]);

    $this->actingAs($user)
        ->get(route('cabinet.orders.show', $order))
        ->assertNotFound();
});
