<?php

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('авторизованный пользователь может создать адрес', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/user/addresses', [
        'label' => 'Дом',
        'address' => 'ул. Тестовая, 123',
        'is_default' => true,
    ]);

    $response->assertStatus(201);
    $response->assertJson(['success' => true]);

    $this->assertDatabaseHas('user_addresses', [
        'user_id' => $user->id,
        'label' => 'Дом',
        'address' => 'ул. Тестовая, 123',
        'is_default' => true,
    ]);
});

it('нельзя создать адрес без авторизации', function () {
    $response = $this->postJson('/user/addresses', [
        'label' => 'Дом',
        'address' => 'ул. Тестовая, 123',
    ]);

    $response->assertStatus(401);
});

it('первый адрес автоматически становится дефолтным', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/user/addresses', [
        'label' => 'Дом',
        'address' => 'ул. Тестовая, 123',
    ]);

    $response->assertStatus(201);

    $address = $user->addresses()->first();
    expect($address->is_default)->toBeTrue();
});

it('можно установить другой адрес как дефолтный', function () {
    $user = User::factory()->create();

    $address1 = UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Дом',
        'address' => 'ул. Тестовая, 123',
        'is_default' => true,
    ]);

    $address2 = UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Работа',
        'address' => 'ул. Рабочая, 456',
        'is_default' => false,
    ]);

    $response = $this->actingAs($user)->postJson("/user/addresses/{$address2->id}/set-default");

    $response->assertSuccessful();

    $address1->refresh();
    $address2->refresh();

    expect($address1->is_default)->toBeFalse();
    expect($address2->is_default)->toBeTrue();
});

it('при установке нового дефолтного, старый сбрасывается', function () {
    $user = User::factory()->create();

    UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Дом',
        'address' => 'ул. Тестовая, 123',
        'is_default' => true,
    ]);

    $response = $this->actingAs($user)->postJson('/user/addresses', [
        'label' => 'Работа',
        'address' => 'ул. Рабочая, 456',
        'is_default' => true,
    ]);

    $response->assertStatus(201);

    $defaultAddresses = $user->addresses()->where('is_default', true)->count();
    expect($defaultAddresses)->toBe(1);
});

it('можно удалить адрес', function () {
    $user = User::factory()->create();

    $address = UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Дом',
        'address' => 'ул. Тестовая, 123',
        'is_default' => false,
    ]);

    $response = $this->actingAs($user)->deleteJson("/user/addresses/{$address->id}");

    $response->assertSuccessful();

    $this->assertDatabaseMissing('user_addresses', [
        'id' => $address->id,
    ]);
});

it('нельзя удалить чужой адрес', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $address = UserAddress::create([
        'user_id' => $user1->id,
        'label' => 'Дом',
        'address' => 'ул. Тестовая, 123',
        'is_default' => false,
    ]);

    $response = $this->actingAs($user2)->deleteJson("/user/addresses/{$address->id}");

    $response->assertStatus(403);

    $this->assertDatabaseHas('user_addresses', [
        'id' => $address->id,
    ]);
});

it('адрес автоматически сохраняется при создании заказа', function () {
    $user = User::factory()->create([
        'phone' => '+995555123456',
    ]);

    $verification = \App\Models\PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    $orderData = [
        'customer_name' => $user->name,
        'customer_phone' => '+995555123456',
        'delivery_type' => 'delivery',
        'delivery_address' => 'ул. Новая, 789',
        'verification_request_id' => $verification->request_id,
        'items' => [
            [
                'type' => 'bowl',
                'id' => 1,
                'name' => 'Тестовый боул',
                'price' => 15.50,
                'quantity' => 1,
                'products' => [],
            ],
        ],
    ];

    $response = $this->actingAs($user)->postJson('/orders', $orderData);

    $response->assertStatus(201);

    $this->assertDatabaseHas('user_addresses', [
        'user_id' => $user->id,
        'address' => 'ул. Новая, 789',
    ]);
});

it('не создаются дубликаты адресов при заказе', function () {
    $user = User::factory()->create([
        'phone' => '+995555123456',
    ]);

    UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Дом',
        'address' => 'ул. Существующая, 123',
        'is_default' => true,
    ]);

    $verification = \App\Models\PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    $orderData = [
        'customer_name' => $user->name,
        'customer_phone' => '+995555123456',
        'delivery_type' => 'delivery',
        'delivery_address' => 'ул. Существующая, 123',
        'verification_request_id' => $verification->request_id,
        'items' => [
            [
                'type' => 'bowl',
                'id' => 1,
                'name' => 'Тестовый боул',
                'price' => 15.50,
                'quantity' => 1,
                'products' => [],
            ],
        ],
    ];

    $response = $this->actingAs($user)->postJson('/orders', $orderData);

    $response->assertStatus(201);

    $addressCount = $user->addresses()->where('address', 'ул. Существующая, 123')->count();
    expect($addressCount)->toBe(1);
});

it('можно получить список адресов пользователя', function () {
    $user = User::factory()->create();

    UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Дом',
        'address' => 'ул. Тестовая, 123',
        'is_default' => true,
    ]);

    UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Работа',
        'address' => 'ул. Рабочая, 456',
        'is_default' => false,
    ]);

    $response = $this->actingAs($user)->getJson('/user/addresses');

    $response->assertSuccessful();
    $response->assertJsonCount(2, 'addresses');
});
