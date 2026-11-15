<?php

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
});

function baseOrderData(array $overrides = []): array
{
    return array_merge([
        'customer_name' => 'Тестовый Клиент',
        'personal_data_consent' => true,
        'customer_phone' => '+995555123456',
        'delivery_type' => 'delivery',
        'delivery_city' => 'Batumi',
        'delivery_street' => 'ул. Тестовая',
        'delivery_house' => '123',
        'verification_method' => 'callback',
        'items' => [
            [
                'type' => 'bowl',
                'id' => 1,
                'name' => 'Тестовый боул',
                'price' => 15.50,
                'quantity' => 1,
            ],
        ],
    ], $overrides);
}

function latestDeliveryTimeOrder(): Order
{
    return Order::query()->latest('id')->firstOrFail();
}

it('сохраняет заказ без времени доставки (как можно быстрее)', function () {
    $response = $this->postJson('/orders', baseOrderData());

    $response->assertCreated();
    $order = latestDeliveryTimeOrder();

    expect($order->customer_phone)->toBe('+995555123456')
        ->and($order->delivery_time)->toBeNull()
        ->and($order->fulfill_at)->toBeNull();
});

it('сохраняет заказ с корректным временем доставки', function () {
    $response = $this->postJson('/orders', baseOrderData(['delivery_time' => '14:30']));

    $response->assertCreated();
    $order = latestDeliveryTimeOrder();

    expect($order->delivery_time)->toBe('14:30')
        ->and($order->fulfill_at->timezone(config('app.timezone'))->format('Y-m-d H:i'))
        ->toBe(now()->setTime(14, 30)->format('Y-m-d H:i'));
});

it('отклоняет слот 10:00 в начале рабочего дня', function () {
    $this->travelTo(now()->setTime(10, 0));

    $this->postJson('/orders', baseOrderData(['delivery_time' => '10:00']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('delivery_time');
});

it('сохраняет ближайшее время 10:30 если заказ оформлен в 10:00', function () {
    $this->travelTo(now()->setTime(10, 0));

    $this->postJson('/orders', baseOrderData(['delivery_time' => '10:30']))
        ->assertCreated();

    $fulfillAt = latestDeliveryTimeOrder()->fulfill_at->timezone(config('app.timezone'));

    expect(latestDeliveryTimeOrder()->delivery_time)->toBe('10:30')
        ->and($fulfillAt->format('H:i'))->toBe('10:30')
        ->and($fulfillAt->isSameDay(now()))->toBeTrue();
});

it('отклоняет заказ как можно быстрее до 10:30', function () {
    $this->travelTo(now()->setTime(10, 0));

    $this->postJson('/orders', baseOrderData())
        ->assertUnprocessable()
        ->assertJsonValidationErrors('delivery_time');
});

it('сохраняет заказ с граничным временем 20:00', function () {
    $response = $this->postJson('/orders', baseOrderData(['delivery_time' => '20:00']));

    $response->assertCreated();

    expect(latestDeliveryTimeOrder()->delivery_time)->toBe('20:00');
});

it('сохраняет заказ с граничным временем 21:00', function () {
    $response = $this->postJson('/orders', baseOrderData(['delivery_time' => '21:00']));

    $response->assertCreated();

    expect(latestDeliveryTimeOrder()->fulfill_at->timezone(config('app.timezone'))->format('H:i'))->toBe('21:00');
});

it('принимает заказ как можно быстрее ровно в 10:30 и в 21:00', function (string $time) {
    $this->travelTo(now()->setTime(...array_map(intval(...), explode(':', $time))));

    $this->postJson('/orders', baseOrderData())->assertCreated();

    expect(latestDeliveryTimeOrder()->fulfill_at)->toBeNull();
})->with(['10:30', '21:00']);

it('отклоняет время доставки вне диапазона 10:30-21:00', function () {
    $response = $this->postJson('/orders', baseOrderData(['delivery_time' => '09:30']));

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('delivery_time');
});

it('отклоняет время доставки после 21:00', function () {
    $response = $this->postJson('/orders', baseOrderData(['delivery_time' => '21:30']));

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('delivery_time');
});

it('отклоняет время в неверном формате', function () {
    $response = $this->postJson('/orders', baseOrderData(['delivery_time' => '2:30pm']));

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors('delivery_time');
});

it('отклоняет уже прошедший слот сегодня', function () {
    $this->travelTo(now()->setTime(15, 0));

    $this->postJson('/orders', baseOrderData(['delivery_time' => '14:30']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('delivery_time');
});

it('отклоняет заказ как можно быстрее вне окна выполнения', function (int $hour, int $minute) {
    $this->travelTo(now()->setTime($hour, $minute));

    $this->postJson('/orders', baseOrderData())
        ->assertUnprocessable()
        ->assertJsonValidationErrors('delivery_time');
})->with([
    'до открытия' => [9, 15],
    'первые полчаса' => [10, 0],
    'после закрытия' => [21, 1],
]);

it('сохраняет слот на сегодня если заказ оформлен до открытия', function () {
    $this->travelTo(now()->setTime(9, 15));

    $this->postJson('/orders', baseOrderData(['delivery_time' => '11:30']))
        ->assertCreated();

    $fulfillAt = latestDeliveryTimeOrder()->fulfill_at->timezone(config('app.timezone'));

    expect($fulfillAt->isSameDay(now()))->toBeTrue()
        ->and($fulfillAt->format('H:i'))->toBe('11:30');
});

it('сохраняет слот на завтра если заказ оформлен после закрытия', function () {
    $this->travelTo(now()->setTime(21, 15));

    $this->postJson('/orders', baseOrderData(['delivery_time' => '10:30']))
        ->assertCreated();

    expect(latestDeliveryTimeOrder()->fulfill_at->timezone(config('app.timezone'))->format('Y-m-d H:i'))
        ->toBe(now()->addDay()->setTime(10, 30)->format('Y-m-d H:i'))
        ->and(latestDeliveryTimeOrder()->scheduledForLabel())->toBe(now()->addDay()->format('d.m.Y').' 10:30');
});

it('открывает сайт со временем по умолчанию если конфиг окна не загружен', function () {
    config([
        'ordering.opens_at' => null,
        'ordering.closes_at' => null,
        'ordering.slot_minutes' => null,
        'ordering.morning_prep_minutes' => null,
    ]);

    $this->travelTo(now()->setTime(9, 0));

    $window = app(\App\Support\OrderWindow::class);

    expect($window->opensAt())->toBe('10:00')
        ->and($window->closesAt())->toBe('21:00')
        ->and($window->firstSlotAt())->toBe('10:30')
        ->and($window->clientPayload()['slots'][0])->toBe('10:30');

    $this->get(route('home'))->assertSuccessful();
});

it('показывает в оформлении предупреждение и выбор времени ближайшего дня', function () {
    $this->get(route('home'))
        ->assertSuccessful()
        ->assertSee('Заказ можно выполнить только в рабочее время с', false)
        ->assertSee('Оформить на выбранное время', false)
        ->assertSee('"firstSlotAt":"10:30"', false)
        ->assertSee('"closesAt":"21:00"', false);
});

it('принимает все допустимые слоты времени', function () {
    $this->travelTo(now()->setTime(10, 30));

    $slots = [];
    for ($minutes = 10 * 60 + 30; $minutes <= 21 * 60; $minutes += 30) {
        $slots[] = sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    foreach ($slots as $slot) {
        $response = $this->postJson('/orders', baseOrderData(['delivery_time' => $slot]));
        $response->assertCreated("Слот {$slot} должен быть разрешён");
    }
});
