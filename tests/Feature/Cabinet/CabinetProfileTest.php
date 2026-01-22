<?php

use App\Models\User;

test('cabinet profile page is displayed', function () {
    $user = User::factory()->create(['phone' => '+995555111333']);

    $this->actingAs($user)
        ->get(route('cabinet.profile.edit'))
        ->assertOk();
});

test('cabinet profile can be updated', function () {
    $user = User::factory()->create([
        'phone' => '+995555222444',
        'email' => 'old@example.com',
    ]);

    $response = $this->actingAs($user)->put(route('cabinet.profile.update'), [
        'name' => 'Updated Name',
        'email' => 'new@example.com',
        'phone' => '+995555222444',
    ]);

    $response->assertSessionHasNoErrors()->assertRedirect(route('cabinet.profile.edit'));

    $user->refresh();
    expect($user->name)->toBe('Updated Name');
    expect($user->email)->toBe('new@example.com');
});

test('cabinet profile validates unique phone', function () {
    $existing = User::factory()->create(['phone' => '+995555333555']);
    $user = User::factory()->create(['phone' => '+995555444666']);

    $response = $this->actingAs($user)->put(route('cabinet.profile.update'), [
        'name' => $user->name,
        'email' => $user->email,
        'phone' => $existing->phone,
    ]);

    $response->assertSessionHasErrors('phone');
});
