<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get('/admin')->assertRedirect('/login');
});

test('authenticated admin users can visit the dashboard', function () {
    $this->actingAs($user = User::factory()->admin()->create());

    $this->get('/admin')->assertStatus(200);
});

test('authenticated regular users cannot visit the dashboard', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/admin')->assertForbidden();
});

test('old dashboard route redirects to admin dashboard for admins', function () {
    $this->actingAs($user = User::factory()->admin()->create());

    $this->get('/dashboard')->assertRedirect('/admin');
});

test('old dashboard route is forbidden for regular users', function () {
    $this->actingAs($user = User::factory()->create());

    $this->get('/dashboard')->assertForbidden();
});
