<?php

use App\Enums\ConstructorType;
use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\ConstructorCategory;
use App\Models\ConstructorProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;

function makeBreakfastOrderWithEggs(?User $user = null): Order
{
    $order = Order::create([
        'user_id' => $user?->id,
        'customer_name' => $user?->name ?? 'Тест Клиент',
        'customer_phone' => $user?->phone ?? '+995555123456',
        'delivery_type' => DeliveryType::Pickup,
        'subtotal' => 15,
        'delivery_fee' => 0,
        'total' => 15,
        'status' => OrderStatus::New,
        'payment_method' => PaymentMethod::Cash,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'breakfast',
        'name' => 'Собранный завтрак',
        'price' => 7.50,
        'quantity' => 2,
        'subtotal' => 15.00,
        'bowl_products' => [
            ['id' => 1, 'name' => 'Яйца пашот', 'price' => 7.50, 'quantity' => 1],
        ],
    ]);

    return $order->fresh('items');
}

it('рендерит хелперы количества и суммы состава конструктора в корзине', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertSee('$store.cart.productQuantity(item, product)', false)
        ->assertSee('$store.cart.productLinePrice(item, product)', false);
});

it('показывает количество и сумму ингредиента за весь заказ в админке', function () {
    $admin = User::factory()->admin()->create();
    $order = makeBreakfastOrderWithEggs();

    $this->actingAs($admin)
        ->get(route('admin.orders.show', $order))
        ->assertSuccessful()
        ->assertSee('Яйца пашот', false)
        ->assertSee('×2', false)
        ->assertSee('15.00 ₾', false);
});

it('показывает количество и сумму ингредиента за весь заказ в кабинете', function () {
    $user = User::factory()->create();
    $order = makeBreakfastOrderWithEggs($user);

    $this->actingAs($user)
        ->get(route('cabinet.orders.show', $order))
        ->assertSuccessful()
        ->assertSee('Яйца пашот', false)
        ->assertSee('×2', false)
        ->assertSee('15.00 ₾', false);
});

it('сохраняет quantity ингредиента при создании конструктора из админки', function () {
    $admin = User::factory()->admin()->create();
    $category = ConstructorCategory::factory()->create([
        'type' => ConstructorType::Breakfast,
    ]);
    $egg = ConstructorProduct::factory()->forCategories($category)->create([
        'name_ru' => 'Яйца пашот',
        'price' => 5.00,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.orders.store'), [
            'customer_name' => 'Тестовый Клиент',
            'customer_phone' => '+995555123456',
            'customer_email' => null,
            'delivery_type' => DeliveryType::Pickup->value,
            'delivery_address' => null,
            'comment' => null,
            'status' => OrderStatus::New->value,
            'items' => [
                [
                    'type' => 'breakfast',
                    'bowl_products' => [$egg->id],
                    'quantity' => 2,
                ],
            ],
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $item = OrderItem::query()->first();

    expect($item)->not->toBeNull()
        ->and($item->quantity)->toBe(2)
        ->and($item->bowl_products[0]['quantity'])->toBe(1)
        ->and($item->bowl_products[0]['name'])->toBe('Яйца пашот');
});
