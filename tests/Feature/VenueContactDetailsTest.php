<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the venue phone and working hours on the storefront', function () {
    $response = $this->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('tel:+995500700877', false);
    $response->assertSee('+995 500 700 877', false);
    $response->assertSee('Пн-Вс 10:00-21:00', false);
    $response->assertSee('Время работы: 10:00 - 21:00', false);
    $response->assertDontSee('tel:+995555123456', false);
    $response->assertDontSee('Пн-Вс 10:00-22:00', false);
    $response->assertDontSee('Время работы: 10:00 - 20:00', false);
    $response->assertDontSee('Телефон: +995 555 123 456', false);
});
