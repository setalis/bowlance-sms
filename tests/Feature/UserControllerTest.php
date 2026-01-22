<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

it('displays users index page', function () {
    $users = User::factory()->count(3)->create();

    $response = $this->actingAs($this->admin)->get(route('admin.users.index'));

    $response->assertSuccessful();
    $response->assertViewIs('users.index');
    $response->assertViewHas('users');
});

it('displays create user form', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.users.create'));

    $response->assertSuccessful();
    $response->assertViewIs('users.create');
    $response->assertViewHas('roles');
});

it('can create a new user', function () {
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '+995 555 123 456',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => UserRole::User->value,
    ];

    $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $data);

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('success', 'Пользователь успешно создан.');

    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '+995 555 123 456',
        'role' => UserRole::User->value,
    ]);
});

it('can create a user without phone', function () {
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => UserRole::User->value,
    ];

    $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $data);

    $response->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => null,
        'role' => UserRole::User->value,
    ]);
});

it('can create an admin user', function () {
    $data = [
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => UserRole::Admin->value,
    ];

    $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $data);

    $response->assertRedirect(route('admin.users.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'role' => UserRole::Admin->value,
    ]);
});

it('validates required fields when creating user', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.users.store'), []);

    $response->assertSessionHasErrors(['name', 'email', 'password', 'role']);
});

it('validates email uniqueness when creating user', function () {
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);

    $data = [
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => UserRole::User->value,
    ];

    $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $data);

    $response->assertSessionHasErrors(['email']);
});

it('validates password confirmation when creating user', function () {
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'different-password',
        'role' => UserRole::User->value,
    ];

    $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $data);

    $response->assertSessionHasErrors(['password']);
});

it('displays edit user form', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($this->admin)->get(route('admin.users.edit', $user));

    $response->assertSuccessful();
    $response->assertViewIs('users.edit');
    $response->assertViewHas('user', $user);
    $response->assertViewHas('roles');
});

it('can update user', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'role' => UserRole::User,
    ]);

    $data = [
        'name' => 'New Name',
        'email' => 'new@example.com',
        'phone' => '+995 555 123 456',
        'role' => UserRole::Admin->value,
    ];

    $response = $this->actingAs($this->admin)->put(route('admin.users.update', $user), $data);

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('success', 'Пользователь успешно обновлен.');

    $user->refresh();
    expect($user->name)->toBe('New Name');
    expect($user->email)->toBe('new@example.com');
    expect($user->phone)->toBe('+995 555 123 456');
    expect($user->role)->toBe(UserRole::Admin);
});

it('can update user phone', function () {
    $user = User::factory()->create(['phone' => '+995 111 111 111']);

    $data = [
        'name' => $user->name,
        'email' => $user->email,
        'phone' => '+995 555 123 456',
        'role' => $user->role->value,
    ];

    $response = $this->actingAs($this->admin)->put(route('admin.users.update', $user), $data);

    $response->assertRedirect(route('admin.users.index'));

    $user->refresh();
    expect($user->phone)->toBe('+995 555 123 456');
});

it('can remove user phone', function () {
    $user = User::factory()->create(['phone' => '+995 111 111 111']);

    $data = [
        'name' => $user->name,
        'email' => $user->email,
        'phone' => '',
        'role' => $user->role->value,
    ];

    $response = $this->actingAs($this->admin)->put(route('admin.users.update', $user), $data);

    $response->assertRedirect(route('admin.users.index'));

    $user->refresh();
    expect($user->phone)->toBeNull();
});

it('validates phone format', function () {
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => 'invalid-phone-format',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => UserRole::User->value,
    ];

    $response = $this->actingAs($this->admin)->post(route('admin.users.store'), $data);

    $response->assertSessionHasErrors(['phone']);
});

it('can update user password', function () {
    $user = User::factory()->create();

    $data = [
        'name' => $user->name,
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
        'role' => $user->role->value,
    ];

    $response = $this->actingAs($this->admin)->put(route('admin.users.update', $user), $data);

    $response->assertRedirect(route('admin.users.index'));

    // Проверяем, что пароль изменился
    $user->refresh();
    expect(\Illuminate\Support\Facades\Hash::check('new-password', $user->password))->toBeTrue();
});

it('does not update password if not provided', function () {
    $user = User::factory()->create();
    $oldPassword = $user->password;

    $data = [
        'name' => 'Updated Name',
        'email' => $user->email,
        'role' => $user->role->value,
    ];

    $response = $this->actingAs($this->admin)->put(route('admin.users.update', $user), $data);

    $response->assertRedirect(route('admin.users.index'));

    $user->refresh();
    expect($user->password)->toBe($oldPassword);
});

it('validates email uniqueness when updating user', function () {
    $user1 = User::factory()->create(['email' => 'user1@example.com']);
    $user2 = User::factory()->create(['email' => 'user2@example.com']);

    $data = [
        'name' => $user2->name,
        'email' => 'user1@example.com',
        'role' => $user2->role->value,
    ];

    $response = $this->actingAs($this->admin)->put(route('admin.users.update', $user2), $data);

    $response->assertSessionHasErrors(['email']);
});

it('requires authentication to access users', function () {
    $response = $this->get(route('admin.users.index'));
    $response->assertRedirect(route('login'));
});

it('requires admin role to access users', function () {
    $regularUser = User::factory()->create();

    $response = $this->actingAs($regularUser)->get(route('admin.users.index'));
    $response->assertForbidden();
});
