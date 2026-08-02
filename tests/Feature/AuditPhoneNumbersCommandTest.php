<?php

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeOrderWithPhone(User $user, string $phone, string $orderNumber): Order
{
    return Order::create([
        'user_id' => $user->id,
        'order_number' => $orderNumber,
        'customer_name' => 'Тест Клиент',
        'customer_phone' => $phone,
        'delivery_type' => DeliveryType::Pickup,
        'subtotal' => 100.00,
        'delivery_fee' => 0,
        'total' => 100.00,
        'status' => OrderStatus::New,
        'payment_method' => PaymentMethod::Cash,
    ]);
}

it('приводит распознанный номер к E.164', function () {
    $user = User::factory()->create(['phone' => '555 94 82 17']);

    $this->artisan('phones:audit', ['--fix' => true])->assertSuccessful();

    expect($user->fresh()->phone)->toBe('+995555948217');
});

it('без --fix ничего не переписывает', function () {
    $user = User::factory()->create(['phone' => '555 94 82 17']);

    $this->artisan('phones:audit')->assertSuccessful();

    expect($user->fresh()->phone)->toBe('555 94 82 17');
});

it('переводит пользователя и его заказы на указанный номер', function () {
    $user = User::factory()->create(['phone' => '+995507082864']);
    $brokenOrder = makeOrderWithPhone($user, '+995507082864', 'ORD-TEST-001');
    $goodOrder = makeOrderWithPhone($user, '+995555948217', 'ORD-TEST-002');

    $this->artisan('phones:audit', ['--set' => ["{$user->id}:+380507082864"]])->assertSuccessful();

    expect($user->fresh()->phone)->toBe('+380507082864')
        ->and($brokenOrder->fresh()->customer_phone)->toBe('+380507082864')
        ->and($goodOrder->fresh()->customer_phone)->toBe('+995555948217');
});

it('не переписывает номер когда E.164 уже занят другим пользователем', function () {
    User::factory()->create(['phone' => '+995555948217']);
    $duplicate = User::factory()->create(['phone' => '555 94 82 17']);

    $this->artisan('phones:audit', ['--fix' => true])->assertSuccessful();

    expect($duplicate->fresh()->phone)->toBe('555 94 82 17');
});

it('отказывается применять несуществующий номер', function () {
    $user = User::factory()->create(['phone' => '+995555948217']);

    $this->artisan('phones:audit', ['--set' => ["{$user->id}:+995222222222"]])->assertFailed();

    expect($user->fresh()->phone)->toBe('+995555948217');
});
