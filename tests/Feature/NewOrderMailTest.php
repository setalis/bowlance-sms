<?php

use App\Mail\NewOrderMail;
use App\Models\PhoneVerification;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
});

function validOrderPayload(string $phone, string $requestId): array
{
    return [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => $phone,
        'customer_email' => 'client@example.com',
        'delivery_type' => 'delivery',
        'delivery_address' => 'Batumi, ул. Руставели, 1',
        'delivery_city' => 'Batumi',
        'delivery_street' => 'ул. Руставели',
        'delivery_house' => '1',
        'verification_request_id' => $requestId,
        'items' => [
            [
                'type' => 'bowl',
                'id' => 1,
                'name' => 'Тестовый боул',
                'price' => 12.00,
                'quantity' => 2,
                'calories' => 500,
                'products' => ['Ingredient A'],
            ],
        ],
    ];
}

it('отправляет письмо администратору после создания заказа', function () {
    Mail::fake();

    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555100200',
    ]);

    $this->postJson('/orders', validOrderPayload('+995555100200', $verification->request_id))
        ->assertStatus(201);

    Mail::assertQueued(NewOrderMail::class, function (NewOrderMail $mail) {
        return $mail->hasTo(config('mail.admin_email'));
    });
});

it('письмо содержит правильный номер заказа в теме', function () {
    Mail::fake();

    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555100201',
    ]);

    $this->postJson('/orders', validOrderPayload('+995555100201', $verification->request_id))
        ->assertStatus(201);

    Mail::assertQueued(NewOrderMail::class, function (NewOrderMail $mail) {
        $order = \App\Models\Order::latest()->first();

        return str_contains($mail->envelope()->subject, $order->order_number);
    });
});

it('не отправляет письмо когда приём заказов отключён', function () {
    Mail::fake();

    Setting::set('orders_enabled', false);

    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555100202',
    ]);

    $this->postJson('/orders', validOrderPayload('+995555100202', $verification->request_id))
        ->assertStatus(503);

    Mail::assertNothingQueued();
});
