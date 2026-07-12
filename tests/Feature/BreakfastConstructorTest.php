<?php

use App\Enums\ConstructorType;
use App\Models\ConstructorCategory;
use App\Models\ConstructorProduct;
use App\Models\OrderItem;
use App\Models\PhoneVerification;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
});

it('показывает категории завтраков только во вкладке конструктора завтраков', function () {
    ConstructorCategory::factory()->create([
        'name_ru' => 'Базы боула',
        'type' => ConstructorType::Bowl,
    ]);

    ConstructorCategory::factory()->create([
        'name_ru' => 'Яйца',
        'type' => ConstructorType::Breakfast,
    ]);

    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee(__('frontend.breakfast_constructor_tab'));
    $response->assertSee("bowlConstructor('breakfast')", false);

    $content = $response->getContent();
    $constructorPos = strpos($content, 'id="constructor-content"');
    $breakfastPos = strpos($content, 'id="breakfast-constructor-content"');

    expect($constructorPos)->not->toBeFalse();
    expect($breakfastPos)->not->toBeFalse();

    $bowlSection = substr($content, $constructorPos, $breakfastPos - $constructorPos);
    $breakfastSection = substr($content, $breakfastPos);

    expect($bowlSection)->toContain('Базы боула');
    expect($bowlSection)->not->toContain('Яйца');
    expect($breakfastSection)->toContain('Яйца');
    expect($breakfastSection)->not->toContain('Базы боула');
});

it('сохраняет заказ с позицией breakfast и bowl_products', function () {
    $verification = PhoneVerification::factory()->verified()->create([
        'phone' => '+995555123456',
    ]);

    $egg = ConstructorProduct::factory()->create(['name_ru' => 'Яйцо']);

    $orderData = [
        'customer_name' => 'Тестовый Клиент',
        'customer_phone' => '+995555123456',
        'customer_email' => 'test@example.com',
        'delivery_type' => 'delivery',
        'delivery_address' => 'Batumi, ул. Тестовая, 123',
        'delivery_city' => 'Batumi',
        'delivery_street' => 'ул. Тестовая',
        'delivery_house' => '123',
        'verification_request_id' => $verification->request_id,
        'items' => [
            [
                'type' => 'breakfast',
                'id' => 1,
                'name' => 'Собранный завтрак',
                'price' => 12.50,
                'quantity' => 1,
                'calories' => 300,
                'products' => [
                    ['id' => $egg->id, 'name' => 'Яйцо', 'price' => 12.50, 'quantity' => 1],
                ],
            ],
        ],
    ];

    $response = $this->postJson('/orders', $orderData);

    $response->assertStatus(201);

    $orderItem = OrderItem::query()->first();

    expect($orderItem)->not->toBeNull();
    expect($orderItem->item_type)->toBe('breakfast');
    expect($orderItem->bowl_products)->toBe([
        ['id' => $egg->id, 'name' => 'Яйцо', 'price' => 12.50, 'quantity' => 1],
    ]);
});

it('сохраняет тип конструктора при создании категории в админке', function () {
    $admin = \App\Models\User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.constructor-categories.store'), [
        'name_ru' => 'Каши',
        'type' => ConstructorType::Breakfast->value,
        'sort_order' => 1,
    ]);

    $response->assertRedirect(route('admin.constructor-categories.index'));

    $category = ConstructorCategory::query()->where('name_ru', 'Каши')->first();

    expect($category)->not->toBeNull();
    expect($category->type)->toBe(ConstructorType::Breakfast);
});
