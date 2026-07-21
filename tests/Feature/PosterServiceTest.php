<?php

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\ConstructorProduct;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\PosterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

function makePosterOrder(array $orderAttributes = []): Order
{
    $user = User::factory()->create();

    return Order::create(array_merge([
        'user_id' => $user->id,
        'order_number' => 'ORD-TEST-001',
        'customer_name' => 'Тест Клиент',
        'customer_phone' => '+995555123456',
        'customer_email' => 'test@example.com',
        'delivery_type' => DeliveryType::Delivery,
        'delivery_address' => 'ул. Тестовая, 1',
        'subtotal' => 500.00,
        'delivery_fee' => 0,
        'total' => 500.00,
        'status' => OrderStatus::New,
        'payment_method' => PaymentMethod::Cash,
        'phone_verified' => true,
        'needs_callback' => false,
        'leave_at_door' => false,
    ], $orderAttributes));
}

it('отправляет заказ в Poster когда блюдо имеет poster_product_id', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'response' => ['incoming_order_id' => '42'],
        ]),
    ]);

    $dish = Dish::factory()->create(['poster_product_id' => 169]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 250.00,
        'quantity' => 2,
        'subtotal' => 500.00,
    ]);

    $service = new PosterService;
    $result = $service->createIncomingOrder($order->load('items.dish'));

    Http::assertSent(function ($request) {
        $body = $request->data();

        return str_contains($request->url(), 'joinposter.com')
            && str_contains($request->url(), 'token=test-token')
            && $body['spot_id'] === 1
            && $body['phone'] === '+995555123456'
            && count($body['products']) === 1
            && $body['products'][0]['product_id'] === 169
            && $body['products'][0]['count'] === 2;
    });

    expect($result)->not->toBeNull();
    expect($order->fresh()->poster_order_id)->toBe('42');
});

it('не отправляет заказ в Poster когда у блюда нет poster_product_id', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
    ]);

    Http::fake();

    $dish = Dish::factory()->create(['poster_product_id' => null]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 250.00,
        'quantity' => 1,
        'subtotal' => 250.00,
    ]);

    $service = new PosterService;
    $result = $service->createIncomingOrder($order->load('items.dish'));

    Http::assertNothingSent();
    expect($result)->toBeNull();
    expect($order->fresh()->poster_order_id)->toBeNull();
});

it('не отправляет заказ в Poster когда интеграция выключена', function () {
    config([
        'poster.enabled' => false,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
    ]);

    Http::fake();

    $dish = Dish::factory()->create(['poster_product_id' => 169]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 250.00,
        'quantity' => 1,
        'subtotal' => 250.00,
    ]);

    $service = new PosterService;
    $result = $service->createIncomingOrder($order->load('items.dish'));

    Http::assertNothingSent();
    expect($result)->toBeNull();
});

it('не отправляет заказ в Poster когда токен не задан', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => null,
        'poster.spot_id' => 1,
    ]);

    Http::fake();

    $dish = Dish::factory()->create(['poster_product_id' => 169]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 250.00,
        'quantity' => 1,
        'subtotal' => 250.00,
    ]);

    $service = new PosterService;
    $result = $service->createIncomingOrder($order->load('items.dish'));

    Http::assertNothingSent();
    expect($result)->toBeNull();
});

it('логирует ошибку когда Poster возвращает error с HTTP 200', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'error' => 32,
            'message' => 'Query parameters error',
        ]),
    ]);

    $dish = Dish::factory()->create(['poster_product_id' => 169]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 250.00,
        'quantity' => 1,
        'subtotal' => 250.00,
    ]);

    $service = new PosterService;
    $result = $service->createIncomingOrder($order->load('items.dish'));

    expect($result)->toBeNull();
    expect($order->fresh()->poster_order_id)->toBeNull();
});

it('пропускает bowl и drink элементы при отправке в Poster', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'response' => ['incoming_order_id' => '99'],
        ]),
    ]);

    $dish = Dish::factory()->create(['poster_product_id' => 200]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 250.00,
        'quantity' => 1,
        'subtotal' => 250.00,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'bowl',
        'dish_id' => null,
        'name' => 'Авторский боул',
        'price' => 300.00,
        'quantity' => 1,
        'subtotal' => 300.00,
        'bowl_products' => [],
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'drink',
        'dish_id' => null,
        'name' => 'Вода',
        'price' => 50.00,
        'quantity' => 2,
        'subtotal' => 100.00,
    ]);

    $service = new PosterService;
    $service->createIncomingOrder($order->load('items.dish'));

    Http::assertSent(function ($request) {
        $body = $request->data();

        return count($body['products']) === 1
            && $body['products'][0]['product_id'] === 200;
    });
});

it('отправляет боул как конструктор с модификаторами', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
        'poster.constructor_product_id' => 74,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'response' => ['incoming_order_id' => '55'],
        ]),
    ]);

    $rice = ConstructorProduct::factory()->create(['poster_bowl_modification_id' => 301]);
    $salmon = ConstructorProduct::factory()->create(['poster_bowl_modification_id' => 302]);

    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'bowl',
        'dish_id' => null,
        'name' => 'Собранный боул',
        'price' => 300.00,
        'quantity' => 2,
        'subtotal' => 600.00,
        'bowl_products' => [
            ['id' => $rice->id, 'name' => 'Рис', 'price' => 100.00, 'quantity' => 1],
            ['id' => $salmon->id, 'name' => 'Лосось', 'price' => 200.00, 'quantity' => 3],
        ],
    ]);

    $service = new PosterService;
    $result = $service->createIncomingOrder($order->load('items.dish'));

    Http::assertSent(function ($request) {
        $body = $request->data();
        $modification = json_decode($body['products'][0]['modification'] ?? '', true);

        return count($body['products']) === 1
            && $body['products'][0]['product_id'] === 74
            && $body['products'][0]['count'] === 2
            && $modification === [
                ['m' => 301, 'a' => 1],
                ['m' => 302, 'a' => 3],
            ];
    });

    expect($result)->not->toBeNull();
    expect($order->fresh()->poster_order_id)->toBe('55');
});

it('сортирует модификаторы по m перед отправкой в Poster', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
        'poster.constructor_product_id' => 74,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'response' => ['incoming_order_id' => '60'],
        ]),
    ]);

    $first = ConstructorProduct::factory()->create(['poster_bowl_modification_id' => 305]);
    $second = ConstructorProduct::factory()->create(['poster_bowl_modification_id' => 301]);

    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'bowl',
        'dish_id' => null,
        'name' => 'Собранный боул',
        'price' => 200.00,
        'quantity' => 1,
        'subtotal' => 200.00,
        'bowl_products' => [
            ['id' => $first->id, 'name' => 'Первый', 'price' => 100.00, 'quantity' => 1],
            ['id' => $second->id, 'name' => 'Второй', 'price' => 100.00, 'quantity' => 1],
        ],
    ]);

    $service = new PosterService;
    $service->createIncomingOrder($order->load('items.dish'));

    Http::assertSent(function ($request) {
        $modification = json_decode($request->data()['products'][0]['modification'] ?? '', true);

        return $modification === [
            ['m' => 301, 'a' => 1],
            ['m' => 305, 'a' => 1],
        ];
    });
});

it('пропускает боул без сопоставленных модификаторов но отправляет блюда', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
        'poster.constructor_product_id' => 74,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'response' => ['incoming_order_id' => '56'],
        ]),
    ]);

    $unmappedProduct = ConstructorProduct::factory()->create(['poster_bowl_modification_id' => null]);
    $dish = Dish::factory()->create(['poster_product_id' => 169]);

    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 250.00,
        'quantity' => 1,
        'subtotal' => 250.00,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'bowl',
        'dish_id' => null,
        'name' => 'Собранный боул',
        'price' => 100.00,
        'quantity' => 1,
        'subtotal' => 100.00,
        'bowl_products' => [
            ['id' => $unmappedProduct->id, 'name' => 'Без маппинга', 'price' => 100.00, 'quantity' => 1],
        ],
    ]);

    $service = new PosterService;
    $service->createIncomingOrder($order->load('items.dish'));

    Http::assertSent(function ($request) {
        $body = $request->data();

        return count($body['products']) === 1
            && $body['products'][0]['product_id'] === 169;
    });
});

it('пропускает боул когда constructor_product_id не настроен', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
        'poster.constructor_product_id' => 0,
    ]);

    Http::fake();

    $rice = ConstructorProduct::factory()->create(['poster_bowl_modification_id' => 301]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'bowl',
        'dish_id' => null,
        'name' => 'Собранный боул',
        'price' => 100.00,
        'quantity' => 1,
        'subtotal' => 100.00,
        'bowl_products' => [
            ['id' => $rice->id, 'name' => 'Рис', 'price' => 100.00, 'quantity' => 1],
        ],
    ]);

    $service = new PosterService;
    $result = $service->createIncomingOrder($order->load('items.dish'));

    Http::assertNothingSent();
    expect($result)->toBeNull();
});

it('отправляет смешанный заказ с блюдом и боулом', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
        'poster.constructor_product_id' => 74,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'response' => ['incoming_order_id' => '57'],
        ]),
    ]);

    $dish = Dish::factory()->create(['poster_product_id' => 169]);
    $rice = ConstructorProduct::factory()->create(['poster_bowl_modification_id' => 301]);

    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 250.00,
        'quantity' => 1,
        'subtotal' => 250.00,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'bowl',
        'dish_id' => null,
        'name' => 'Собранный боул',
        'price' => 100.00,
        'quantity' => 1,
        'subtotal' => 100.00,
        'bowl_products' => [
            ['id' => $rice->id, 'name' => 'Рис', 'price' => 100.00, 'quantity' => 2],
        ],
    ]);

    $service = new PosterService;
    $result = $service->createIncomingOrder($order->load('items.dish'));

    Http::assertSent(function ($request) {
        $body = $request->data();
        $modification = json_decode($body['products'][1]['modification'] ?? '', true);

        return count($body['products']) === 2
            && $body['products'][0]['product_id'] === 169
            && $body['products'][1]['product_id'] === 74
            && $modification === [['m' => 301, 'a' => 2]];
    });

    expect($result)->not->toBeNull();
    expect($order->fresh()->poster_order_id)->toBe('57');
});

it('отправляет завтрак как конструктор с product_id 131', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
        'poster.breakfast_constructor_product_id' => 131,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'response' => ['incoming_order_id' => '58'],
        ]),
    ]);

    $egg = ConstructorProduct::factory()->create(['poster_breakfast_modification_id' => 401]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'breakfast',
        'dish_id' => null,
        'name' => 'Собранный завтрак',
        'price' => 150.00,
        'quantity' => 1,
        'subtotal' => 150.00,
        'bowl_products' => [
            ['id' => $egg->id, 'name' => 'Яйцо', 'price' => 150.00, 'quantity' => 1],
        ],
    ]);

    $service = new PosterService;
    $result = $service->createIncomingOrder($order->load('items.dish'));

    Http::assertSent(function ($request) {
        $body = $request->data();
        $modification = json_decode($body['products'][0]['modification'] ?? '', true);

        return count($body['products']) === 1
            && $body['products'][0]['product_id'] === 131
            && $modification === [['m' => 401, 'a' => 1]];
    });

    expect($result)->not->toBeNull();
    expect($order->fresh()->poster_order_id)->toBe('58');
});

it('отправляет смешанный заказ с боулом и завтраком', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
        'poster.constructor_product_id' => 74,
        'poster.breakfast_constructor_product_id' => 131,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'response' => ['incoming_order_id' => '59'],
        ]),
    ]);

    $rice = ConstructorProduct::factory()->create(['poster_bowl_modification_id' => 301]);
    $egg = ConstructorProduct::factory()->create(['poster_breakfast_modification_id' => 401]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'bowl',
        'dish_id' => null,
        'name' => 'Собранный боул',
        'price' => 100.00,
        'quantity' => 1,
        'subtotal' => 100.00,
        'bowl_products' => [
            ['id' => $rice->id, 'name' => 'Рис', 'price' => 100.00, 'quantity' => 1],
        ],
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'breakfast',
        'dish_id' => null,
        'name' => 'Собранный завтрак',
        'price' => 150.00,
        'quantity' => 1,
        'subtotal' => 150.00,
        'bowl_products' => [
            ['id' => $egg->id, 'name' => 'Яйцо', 'price' => 150.00, 'quantity' => 2],
        ],
    ]);

    $service = new PosterService;
    $result = $service->createIncomingOrder($order->load('items.dish'));

    Http::assertSent(function ($request) {
        $body = $request->data();
        $bowlModification = json_decode($body['products'][0]['modification'] ?? '', true);
        $breakfastModification = json_decode($body['products'][1]['modification'] ?? '', true);

        return count($body['products']) === 2
            && $body['products'][0]['product_id'] === 74
            && $body['products'][1]['product_id'] === 131
            && $bowlModification === [['m' => 301, 'a' => 1]]
            && $breakfastModification === [['m' => 401, 'a' => 2]];
    });

    expect($result)->not->toBeNull();
});

it('для общего продукта использует разные modification id в боуле и завтраке', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
        'poster.constructor_product_id' => 74,
        'poster.breakfast_constructor_product_id' => 131,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'response' => ['incoming_order_id' => '61'],
        ]),
    ]);

    $buckwheat = ConstructorProduct::factory()->create([
        'poster_bowl_modification_id' => 301,
        'poster_breakfast_modification_id' => 501,
    ]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'bowl',
        'dish_id' => null,
        'name' => 'Собранный боул',
        'price' => 100.00,
        'quantity' => 1,
        'subtotal' => 100.00,
        'bowl_products' => [
            ['id' => $buckwheat->id, 'name' => 'Гречка', 'price' => 100.00, 'quantity' => 1],
        ],
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'breakfast',
        'dish_id' => null,
        'name' => 'Собранный завтрак',
        'price' => 100.00,
        'quantity' => 1,
        'subtotal' => 100.00,
        'bowl_products' => [
            ['id' => $buckwheat->id, 'name' => 'Гречка', 'price' => 100.00, 'quantity' => 2],
        ],
    ]);

    $service = new PosterService;
    $result = $service->createIncomingOrder($order->load('items.dish'));

    Http::assertSent(function ($request) {
        $body = $request->data();
        $bowlModification = json_decode($body['products'][0]['modification'] ?? '', true);
        $breakfastModification = json_decode($body['products'][1]['modification'] ?? '', true);

        return count($body['products']) === 2
            && $body['products'][0]['product_id'] === 74
            && $body['products'][1]['product_id'] === 131
            && $bowlModification === [['m' => 301, 'a' => 1]]
            && $breakfastModification === [['m' => 501, 'a' => 2]];
    });

    expect($result)->not->toBeNull();
});

it('не использует bowl modification id для завтрака и наоборот', function () {
    config([
        'poster.enabled' => true,
        'poster.token' => 'test-token',
        'poster.spot_id' => 1,
        'poster.constructor_product_id' => 74,
        'poster.breakfast_constructor_product_id' => 131,
    ]);

    Http::fake([
        'joinposter.com/*' => Http::response([
            'response' => ['incoming_order_id' => '62'],
        ]),
    ]);

    $onlyBowlMapped = ConstructorProduct::factory()->create([
        'poster_bowl_modification_id' => 301,
        'poster_breakfast_modification_id' => null,
    ]);
    $dish = Dish::factory()->create(['poster_product_id' => 169]);
    $order = makePosterOrder();

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'dish',
        'dish_id' => $dish->id,
        'name' => 'Тестовое блюдо',
        'price' => 250.00,
        'quantity' => 1,
        'subtotal' => 250.00,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'item_type' => 'breakfast',
        'dish_id' => null,
        'name' => 'Собранный завтрак',
        'price' => 100.00,
        'quantity' => 1,
        'subtotal' => 100.00,
        'bowl_products' => [
            ['id' => $onlyBowlMapped->id, 'name' => 'Гречка', 'price' => 100.00, 'quantity' => 1],
        ],
    ]);

    $service = new PosterService;
    $service->createIncomingOrder($order->load('items.dish'));

    Http::assertSent(function ($request) {
        $body = $request->data();

        return count($body['products']) === 1
            && $body['products'][0]['product_id'] === 169;
    });
});
