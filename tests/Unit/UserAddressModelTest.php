<?php

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('имеет отношение к пользователю', function () {
    $user = User::factory()->create();
    $address = UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Дом',
        'address' => 'ул. Тестовая, 123',
        'is_default' => false,
    ]);

    expect($address->user)->toBeInstanceOf(User::class);
    expect($address->user->id)->toBe($user->id);
});

it('пользователь имеет отношение к адресам', function () {
    $user = User::factory()->create();

    UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Дом',
        'address' => 'ул. Тестовая, 123',
        'is_default' => false,
    ]);

    UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Работа',
        'address' => 'ул. Рабочая, 456',
        'is_default' => false,
    ]);

    expect($user->addresses)->toHaveCount(2);
    expect($user->addresses->first())->toBeInstanceOf(UserAddress::class);
});

it('scope default возвращает дефолтный адрес', function () {
    $user = User::factory()->create();

    UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Дом',
        'address' => 'ул. Тестовая, 123',
        'is_default' => false,
    ]);

    $defaultAddress = UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Работа',
        'address' => 'ул. Рабочая, 456',
        'is_default' => true,
    ]);

    $result = UserAddress::default()->first();

    expect($result)->toBeInstanceOf(UserAddress::class);
    expect($result->id)->toBe($defaultAddress->id);
    expect($result->is_default)->toBeTrue();
});

it('scope forUser возвращает адреса пользователя', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    UserAddress::create([
        'user_id' => $user1->id,
        'label' => 'Дом 1',
        'address' => 'ул. Тестовая, 123',
        'is_default' => false,
    ]);

    UserAddress::create([
        'user_id' => $user1->id,
        'label' => 'Работа 1',
        'address' => 'ул. Рабочая, 456',
        'is_default' => false,
    ]);

    UserAddress::create([
        'user_id' => $user2->id,
        'label' => 'Дом 2',
        'address' => 'ул. Другая, 789',
        'is_default' => false,
    ]);

    $addresses = UserAddress::forUser($user1->id)->get();

    expect($addresses)->toHaveCount(2);
    expect($addresses->pluck('user_id')->unique()->first())->toBe($user1->id);
});

it('приводит is_default к boolean', function () {
    $user = User::factory()->create();

    $address = UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Дом',
        'address' => 'ул. Тестовая, 123',
        'is_default' => 1,
    ]);

    $address->refresh();

    expect($address->is_default)->toBeTrue();
    expect($address->is_default)->toBeBool();
});

it('пользователь может получить дефолтный адрес через отношение', function () {
    $user = User::factory()->create();

    UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Дом',
        'address' => 'ул. Тестовая, 123',
        'is_default' => false,
    ]);

    $defaultAddress = UserAddress::create([
        'user_id' => $user->id,
        'label' => 'Работа',
        'address' => 'ул. Рабочая, 456',
        'is_default' => true,
    ]);

    $result = $user->defaultAddress;

    expect($result)->toBeInstanceOf(UserAddress::class);
    expect($result->id)->toBe($defaultAddress->id);
});
