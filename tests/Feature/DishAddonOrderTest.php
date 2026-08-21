<?php

use App\Models\Dish;
use App\Models\DishAddon;
use App\Models\DishCategory;
use App\Models\OrderItem;
use App\Models\PhoneVerification;
use App\Services\PosterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
});

it('saves dish addons and recalculates price on checkout', function () {
    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    $category = DishCategory::factory()->create();
    $dish = Dish::factory()->create([
        'dish_category_id' => $category->id,
        'price' => 20.00,
        'discount_price' => null,
        'name_ru' => 'Суп',
        'calories' => 400,
        'proteins' => 10.00,
        'fats' => 5.00,
        'carbohydrates' => 20.00,
    ]);
    $addon = DishAddon::factory()->create([
        'name_ru' => 'Хлеб',
        'price' => 2.00,
        'calories' => 80,
        'proteins' => 6.50,
        'fats' => 2.00,
        'carbohydrates' => 1.00,
    ]);
    $dish->addons()->attach($addon->id, [
        'poster_modification_id' => 901,
        'price' => null,
        'sort_order' => 0,
    ]);

    $response = $this->postJson('/orders', [
        'customer_name' => 'Тест',
        'customer_phone' => '+995555123456',
        'delivery_type' => 'pickup',
        'verification_request_id' => $verification->request_id,
        'items' => [
            [
                'type' => 'dish',
                'id' => $dish->id,
                'name' => 'Суп',
                'price' => 999,
                'quantity' => 1,
                'addons' => [
                    ['id' => $addon->id, 'quantity' => 2, 'name' => 'Хлеб', 'price' => 2],
                ],
            ],
        ],
    ]);

    $response->assertCreated();

    $orderItem = OrderItem::query()->first();

    expect($orderItem)->not->toBeNull()
        ->and((float) $orderItem->price)->toBe(24.0)
        ->and($orderItem->calories)->toBe(560)
        ->and((float) $orderItem->proteins)->toBe(23.0)
        ->and((float) $orderItem->fats)->toBe(9.0)
        ->and((float) $orderItem->carbohydrates)->toBe(22.0)
        ->and($orderItem->dish_addons)->toHaveCount(1)
        ->and($orderItem->dish_addons[0]['id'])->toBe($addon->id)
        ->and($orderItem->dish_addons[0]['name'])->toBe('Хлеб')
        ->and((float) $orderItem->dish_addons[0]['price'])->toBe(2.0)
        ->and($orderItem->dish_addons[0]['quantity'])->toBe(2)
        ->and($orderItem->dish_addons[0]['calories'])->toBe(80);
});

it('includes addon nutrition in the homepage dish payload', function () {
    $category = DishCategory::factory()->active()->create();
    $dish = Dish::factory()->create([
        'dish_category_id' => $category->id,
        'calories' => 400,
        'proteins' => 10,
        'fats' => 5,
        'carbohydrates' => 20,
    ]);
    $addon = DishAddon::factory()->create([
        'name_ru' => 'Креветки',
        'calories' => 187,
        'proteins' => 12.5,
        'fats' => 3.1,
        'carbohydrates' => 1.4,
        'is_active' => true,
    ]);
    $dish->addons()->attach($addon->id, [
        'poster_modification_id' => 1,
        'price' => null,
        'sort_order' => 0,
    ]);

    $content = $this->get('/')->assertSuccessful()->getContent();

    expect($content)
        ->toContain('187')
        ->toContain('12.5')
        ->toContain('3.1')
        ->toContain('1.4')
        ->toContain('data-addons')
        ->toContain('addon.calories || 0')
        ->toContain('addon.proteins || 0')
        ->toContain('addon.fats || 0')
        ->toContain('addon.carbs || 0')
        ->toContain('selectedAddonsNutrition');
});

it('renders addon nutrition grid in the modal even when values are empty', function () {
    $category = DishCategory::factory()->active()->create();
    $dish = Dish::factory()->create([
        'dish_category_id' => $category->id,
    ]);
    $addon = DishAddon::factory()->create([
        'name_ru' => 'Укроп',
        'calories' => null,
        'proteins' => null,
        'fats' => null,
        'carbohydrates' => null,
        'is_active' => true,
    ]);
    $dish->addons()->attach($addon->id, [
        'poster_modification_id' => 1,
        'price' => null,
        'sort_order' => 0,
    ]);

    $this->get('/')
        ->assertSuccessful()
        ->assertSee('addon.calories || 0', false)
        ->assertSee('data-addons', false);
});

it('sends dish modifications to Poster for selected addons', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'response' => ['incoming_order_id' => '77'],
        ]),
    ]);

    $category = DishCategory::factory()->create();
    $dish = Dish::factory()->create([
        'dish_category_id' => $category->id,
        'poster_product_id' => 169,
    ]);
    $addon = DishAddon::factory()->create(['name_ru' => 'Укроп']);
    $dish->addons()->attach($addon->id, [
        'poster_modification_id' => 555,
        'price' => 1.00,
        'sort_order' => 0,
    ]);

    $user = \App\Models\User::factory()->create();
    $order = \App\Models\Order::create([
        'user_id' => $user->id,
        'order_number' => 'ORD-ADDON-1',
        'customer_name' => 'Тест',
        'customer_phone' => '+995555123456',
        'delivery_type' => \App\Enums\DeliveryType::Pickup,
        'subtotal' => 20,
        'delivery_fee' => 0,
        'total' => 20,
        'status' => \App\Enums\OrderStatus::New,
        'payment_method' => \App\Enums\PaymentMethod::Cash,
        'phone_verified' => true,
        'needs_callback' => false,
        'leave_at_door' => false,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Суп',
        'price' => 21,
        'quantity' => 1,
        'subtotal' => 21,
        'dish_addons' => [
            ['id' => $addon->id, 'name' => 'Укроп', 'price' => 1, 'quantity' => 2],
        ],
    ]);

    app(PosterService::class)->createIncomingOrder($order->load('items.dish'));

    Http::assertSent(function ($request) {
        $body = $request->data();
        $modification = json_decode($body['products'][0]['modification'] ?? '', true);

        return $body['products'][0]['product_id'] === 169
            && $modification === [['m' => 555, 'a' => 2]];
    });
});

it('does not add modification key for dish without addons', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'response' => ['incoming_order_id' => '78'],
        ]),
    ]);

    $dish = Dish::factory()->create(['poster_product_id' => 169]);
    $user = \App\Models\User::factory()->create();
    $order = \App\Models\Order::create([
        'user_id' => $user->id,
        'order_number' => 'ORD-ADDON-2',
        'customer_name' => 'Тест',
        'customer_phone' => '+995555123456',
        'delivery_type' => \App\Enums\DeliveryType::Pickup,
        'subtotal' => 20,
        'delivery_fee' => 0,
        'total' => 20,
        'status' => \App\Enums\OrderStatus::New,
        'payment_method' => \App\Enums\PaymentMethod::Cash,
        'phone_verified' => true,
        'needs_callback' => false,
        'leave_at_door' => false,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Суп',
        'price' => 20,
        'quantity' => 1,
        'subtotal' => 20,
        'dish_addons' => null,
    ]);

    app(PosterService::class)->createIncomingOrder($order->load('items.dish'));

    Http::assertSent(function ($request) {
        $body = $request->data();

        return $body['products'][0]['product_id'] === 169
            && ! array_key_exists('modification', $body['products'][0]);
    });
});
