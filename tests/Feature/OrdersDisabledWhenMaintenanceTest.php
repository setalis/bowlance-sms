<?php

use App\Models\PhoneVerification;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns 503 and message when orders are disabled', function () {
    Setting::set('orders_enabled', false);

    $verification = PhoneVerification::create([
        'phone' => '+995555123456',
        'request_id' => 'test-request-id-123',
        'verified' => true,
        'verified_at' => now(),
        'expires_at' => now()->addHour(),
    ]);

    $payload = [
        'customer_name' => 'Test User',
        'customer_phone' => '+995555123456',
        'customer_email' => null,
        'delivery_type' => 'pickup',
        'delivery_address' => null,
        'delivery_city' => null,
        'delivery_street' => null,
        'verification_request_id' => $verification->request_id,
        'items' => [
            [
                'type' => 'dish',
                'id' => 1,
                'name' => 'Test Dish',
                'price' => 10.5,
                'quantity' => 1,
                'calories' => 100,
                'proteins' => 5,
                'fats' => 2,
                'carbs' => 10,
            ],
        ],
    ];

    $response = $this->postJson(route('orders.store'), $payload);

    $response->assertStatus(503);
    $response->assertJson([
        'success' => false,
        'message' => __('frontend.orders_disabled_message'),
    ]);
});
