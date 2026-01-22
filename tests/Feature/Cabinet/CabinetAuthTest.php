<?php

use App\Models\User;

test('cabinet login page can be rendered', function () {
    $this->get(route('cabinet.login'))->assertOk();
});

test('cabinet user can authenticate with phone and password', function () {
    $user = User::factory()->create(['phone' => '+995555111222']);

    $response = $this->post(route('cabinet.login'), [
        'phone' => $user->phone,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('cabinet.dashboard', absolute: false));
});

test('admin cannot login via cabinet', function () {
    $admin = User::factory()->admin()->create(['phone' => '+995555333444']);

    $response = $this->post(route('cabinet.login'), [
        'phone' => $admin->phone,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('phone');
    $this->assertGuest();
});

test('cabinet user cannot authenticate with invalid password', function () {
    $user = User::factory()->create(['phone' => '+995555555666']);

    $this->post(route('cabinet.login'), [
        'phone' => $user->phone,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('logged-in cabinet user is redirected from login to dashboard', function () {
    $user = User::factory()->create(['phone' => '+995555777888']);

    $response = $this->actingAs($user)->get(route('cabinet.login'));

    $response->assertRedirect(route('cabinet.dashboard', absolute: false));
});

test('cabinet user can logout', function () {
    $user = User::factory()->create(['phone' => '+995555999000']);

    $response = $this->actingAs($user)->post(route('cabinet.logout'));

    $this->assertGuest();
    $response->assertRedirect(route('cabinet.login'));
});

test('admin cannot access cabinet routes', function () {
    $admin = User::factory()->admin()->create(['phone' => '+995555000111']);

    $this->actingAs($admin)
        ->get(route('cabinet.dashboard'))
        ->assertForbidden();
});
