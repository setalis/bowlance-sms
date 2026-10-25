<?php

use App\Enums\DeliveryType;
use App\Enums\DiscountScope;
use App\Enums\DiscountType;
use App\Enums\OrderMode;
use App\Enums\OrderStatus;
use App\Models\Discount;
use App\Models\Dish;
use App\Models\DishCategory;
use App\Models\Drink;
use App\Models\Order;
use App\Models\OrderItem;

it('shows the order mode selection screen to a new visitor', function () {
    $this->openCatalogByDefault = false;

    $this->get('/')
        ->assertSuccessful()
        ->assertViewIs('frontend.order-mode')
        ->assertSee(__('frontend.order_mode_onsite'), false)
        ->assertSee(__('frontend.order_mode_delivery'), false)
        ->assertSee('storage/images/slider/slider-d-3.webp', false)
        ->assertSee('backdrop-blur-md', false)
        ->assertDontSee('<h1', false);
});

it('saves onsite mode and opens the catalog', function () {
    $response = $this->post(route('order-mode.store'), [
        'order_mode' => OrderMode::Onsite->value,
    ]);

    $response->assertRedirect(route('home'))
        ->assertCookie(OrderMode::WINDOW_COOKIE, '1');

    $cookie = collect($response->headers->getCookies())->first(
        fn ($cookie) => $cookie->getName() === OrderMode::WINDOW_COOKIE,
    );

    expect($cookie)->not->toBeNull()
        ->and($cookie->getExpiresTime())->toBe(0)
        ->and(session(OrderMode::SESSION_KEY))->toBe(OrderMode::Onsite->value);

    $this->get(route('home'))->assertSuccessful();
});

it('saves delivery mode and opens the catalog', function () {
    $this->post(route('order-mode.store'), [
        'order_mode' => OrderMode::Delivery->value,
    ])->assertRedirect(route('home'));

    expect(session(OrderMode::SESSION_KEY))->toBe(OrderMode::Delivery->value);
});

it('skips the selection screen when a valid mode is already stored', function () {
    $this->openCatalogByDefault = false;

    $this->withSession([OrderMode::SESSION_KEY => OrderMode::Delivery->value]);

    $this->get('/')
        ->assertRedirect(route('home'));
});

it('shows the selection screen again after the browser window is closed', function () {
    $this->openCatalogByDefault = false;
    $this->keepOrderModeWindow = false;

    $this->withSession([OrderMode::SESSION_KEY => OrderMode::Delivery->value]);

    $this->get('/')
        ->assertSuccessful()
        ->assertViewIs('frontend.order-mode');

    expect(session(OrderMode::SESSION_KEY))->toBeNull();
});

it('rejects an invalid order mode', function () {
    $this->post(route('order-mode.store'), [
        'order_mode' => 'courier',
    ])->assertSessionHasErrors('order_mode');

    expect(session(OrderMode::SESSION_KEY))->toBeNull();
});

it('opens the catalog with onsite prices from the qr route', function () {
    $category = DishCategory::factory()->create(['is_active' => true]);
    $dish = Dish::factory()->create([
        'dish_category_id' => $category->id,
        'name_ru' => 'Кинбоул',
        'price' => 80,
        'discount_price' => null,
        'is_active' => true,
    ]);

    Discount::factory()->create([
        'size' => 15,
        'type' => DiscountType::Percent,
        'scope' => DiscountScope::Pickup,
    ]);

    $this->get(route('qr.onsite'))
        ->assertRedirect(route('home'));

    expect(session(OrderMode::SESSION_KEY))->toBe(OrderMode::Onsite->value);

    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('68.00')
        ->assertSee($dish->name);
});

it('lets qr entry override a previously selected delivery mode', function () {
    $this->withSession([OrderMode::SESSION_KEY => OrderMode::Delivery->value]);

    $this->get(route('qr.onsite'))->assertRedirect(route('home'));

    expect(session(OrderMode::SESSION_KEY))->toBe(OrderMode::Onsite->value);
});

it('renders a full-width order mode switch below the header', function () {
    $response = $this->withSession([OrderMode::SESSION_KEY => OrderMode::Delivery->value])
        ->get(route('home'));

    $response->assertSuccessful()
        ->assertSee('data-order-mode-switch="bar"', false)
        ->assertSee('data-order-mode-switch="header"', false);

    $html = $response->getContent();
    $barTag = substr($html, strpos($html, 'data-order-mode-switch="bar"') - 120, 180);

    expect($barTag)
        ->toContain('md:hidden')
        ->toContain('w-full')
        ->not->toContain('fixed')
        ->not->toContain('sticky');
});

it('shows delivery prices and onsite prices for the same dish', function () {
    $category = DishCategory::factory()->create(['is_active' => true]);
    Dish::factory()->create([
        'dish_category_id' => $category->id,
        'name_ru' => 'Кинбоул',
        'price' => 80,
        'discount_price' => null,
        'is_active' => true,
    ]);

    Discount::factory()->create([
        'size' => 15,
        'type' => DiscountType::Percent,
        'scope' => DiscountScope::Pickup,
    ]);

    $this->withSession([OrderMode::SESSION_KEY => OrderMode::Delivery->value])
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSee('80.00')
        ->assertDontSee('68.00');

    $this->withSession([OrderMode::SESSION_KEY => OrderMode::Onsite->value])
        ->get(route('home'))
        ->assertSuccessful()
        ->assertSee('68.00');
});

it('recalculates cart lines for the selected mode', function () {
    $category = DishCategory::factory()->create();
    $dish = Dish::factory()->create([
        'dish_category_id' => $category->id,
        'price' => 80,
        'discount_price' => null,
    ]);

    Discount::factory()->create([
        'size' => 15,
        'type' => DiscountType::Percent,
        'scope' => DiscountScope::Pickup,
    ]);

    $this->withSession([OrderMode::SESSION_KEY => OrderMode::Onsite->value])
        ->postJson(route('cart.reprice'), [
            'items' => [[
                'type' => 'dish',
                'id' => $dish->id,
                'name' => 'Чужая цена',
                'price' => 1,
                'quantity' => 2,
            ]],
        ])
        ->assertSuccessful()
        ->assertJsonPath('items.0.price', 68)
        ->assertJsonPath('total', 136);
});

it('ignores a client submitted price when creating an order', function () {
    $category = DishCategory::factory()->create();
    $dish = Dish::factory()->create([
        'dish_category_id' => $category->id,
        'price' => 80,
        'discount_price' => null,
        'name_ru' => 'Кинбоул',
    ]);

    Discount::factory()->create([
        'size' => 15,
        'type' => DiscountType::Percent,
        'scope' => DiscountScope::Pickup,
    ]);

    $this->withSession([OrderMode::SESSION_KEY => OrderMode::Onsite->value])
        ->postJson(route('orders.store'), [
            'customer_name' => 'Нино',
            'personal_data_consent' => true,
            'customer_phone' => '+995555123456',
            'delivery_type' => 'pickup',
            'items' => [[
                'type' => 'dish',
                'id' => $dish->id,
                'name' => 'Кинбоул',
                'price' => 1,
                'quantity' => 1,
            ]],
        ])
        ->assertCreated()
        ->assertJsonPath('order.total', '68.00');

    expect((float) OrderItem::query()->first()->price)->toBe(68.0);
});

it('keeps the charged item price after catalog prices change', function () {
    $category = DishCategory::factory()->create();
    $dish = Dish::factory()->create([
        'dish_category_id' => $category->id,
        'price' => 80,
        'discount_price' => null,
    ]);

    $order = Order::create([
        'customer_name' => 'Нино',
        'customer_phone' => '+995555123456',
        'delivery_type' => DeliveryType::Pickup,
        'order_mode' => OrderMode::Onsite,
        'subtotal' => 68,
        'delivery_fee' => 0,
        'total' => 68,
        'status' => OrderStatus::New,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Кинбоул',
        'price' => 68,
        'quantity' => 1,
        'subtotal' => 68,
    ]);

    $dish->update(['price' => 120]);
    Discount::factory()->create([
        'size' => 10,
        'type' => DiscountType::Percent,
        'scope' => DiscountScope::Pickup,
    ]);

    expect((float) $order->items()->first()->price)->toBe(68.0);
});

it('rejects checkout when the delivery type does not match the order mode', function () {
    $this->inferOrderModeFromDeliveryType = false;

    $category = DishCategory::factory()->create();
    $dish = Dish::factory()->create([
        'dish_category_id' => $category->id,
        'price' => 40,
        'discount_price' => null,
    ]);

    $this->withSession([OrderMode::SESSION_KEY => OrderMode::Onsite->value])
        ->postJson(route('orders.store'), [
            'customer_name' => 'Нино',
            'personal_data_consent' => true,
            'customer_phone' => '+995555123456',
            'delivery_type' => 'delivery',
            'delivery_city' => 'Batumi',
            'delivery_street' => 'ул. Тестовая',
            'delivery_house' => '1',
            'verification_method' => 'callback',
            'items' => [[
                'type' => 'dish',
                'id' => $dish->id,
                'name' => 'Кинбоул',
                'price' => 40,
                'quantity' => 1,
            ]],
        ])
        ->assertJsonValidationErrors('delivery_type');
});

it('rejects checkout without an order mode', function () {
    $this->inferOrderModeFromDeliveryType = false;
    $this->openCatalogByDefault = false;
    session()->forget(OrderMode::SESSION_KEY);

    $category = DishCategory::factory()->create();
    $dish = Dish::factory()->create([
        'dish_category_id' => $category->id,
        'price' => 40,
        'discount_price' => null,
    ]);

    $this->postJson(route('orders.store'), [
        'customer_name' => 'Нино',
        'personal_data_consent' => true,
        'customer_phone' => '+995555123456',
        'delivery_type' => 'delivery',
        'delivery_city' => 'Batumi',
        'delivery_street' => 'ул. Тестовая',
        'delivery_house' => '1',
        'verification_method' => 'callback',
        'items' => [[
            'type' => 'dish',
            'id' => $dish->id,
            'name' => 'Кинбоул',
            'price' => 1,
            'quantity' => 1,
        ]],
    ])->assertJsonValidationErrors('order_mode');
});

it('prices a drink from the catalog instead of the posted price', function () {
    $drink = Drink::factory()->create([
        'price' => 10,
        'discount_price' => null,
        'name_ru' => 'Боржоми',
    ]);

    $this->withSession([OrderMode::SESSION_KEY => OrderMode::Delivery->value])
        ->postJson(route('orders.store'), [
            'customer_name' => 'Нино',
            'personal_data_consent' => true,
            'customer_phone' => '+995555123456',
            'delivery_type' => 'delivery',
            'delivery_city' => 'Batumi',
            'delivery_street' => 'ул. Тестовая',
            'delivery_house' => '1',
            'verification_method' => 'callback',
            'items' => [[
                'type' => 'drink',
                'id' => $drink->id,
                'name' => 'Боржоми',
                'price' => 1,
                'quantity' => 1,
            ]],
        ])
        ->assertCreated();

    expect((float) OrderItem::query()->first()->price)->toBe(10.0);
});
