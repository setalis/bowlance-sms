<?php

use App\Enums\DeliveryType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function () {
    Http::fake();
    $this->admin = User::factory()->admin()->create();
});

function makeAdminOrder(string $phone): Order
{
    return Order::create([
        'user_id' => User::factory()->create()->id,
        'order_number' => 'ORD-ADMIN-001',
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

function adminOrderPayload(string $phone): array
{
    $dish = Dish::factory()->create(['price' => 25.00, 'discount_price' => null]);

    return [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => $phone,
        'customer_email' => null,
        'delivery_type' => 'pickup',
        'delivery_address' => null,
        'comment' => null,
        'status' => OrderStatus::New->value,
        'items' => [
            ['type' => 'dish', 'dish_id' => $dish->id, 'quantity' => 1],
        ],
    ];
}

it('показывает выбор страны в форме создания заказа', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.orders.create'))
        ->assertSuccessful()
        ->assertSee('phoneField(', escape: false);
});

it('подставляет сохранённый номер в форму редактирования', function () {
    $order = makeAdminOrder('+380507082864');

    $this->actingAs($this->admin)
        ->get(route('admin.orders.edit', $order))
        ->assertSuccessful()
        ->assertSee("phoneField('+380507082864', 'UA')", escape: false);
});

it('создаёт заказ из админки с международным номером', function () {
    $this->actingAs($this->admin)
        ->post(route('admin.orders.store'), adminOrderPayload('+380507082864'))
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    expect(Order::query()->first()->customer_phone)->toBe('+380507082864');
});

it('возвращает форму с ошибкой и старым вводом когда номера не существует', function () {
    $payload = adminOrderPayload('0507082864');

    $this->actingAs($this->admin)
        ->from(route('admin.orders.create'))
        ->post(route('admin.orders.store'), $payload)
        ->assertRedirect(route('admin.orders.create'))
        ->assertSessionHasErrors('customer_phone')
        ->assertSessionHasInput('customer_name', 'Тестовый Клиент')
        ->assertSessionHasInput('customer_phone', '0507082864');

    expect(Order::query()->count())->toBe(0);
});
