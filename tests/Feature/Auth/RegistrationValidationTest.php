<?php

use App\Models\User;

test('registration requires name', function () {
    $this->post('/register', [
        'name' => '',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('name');
});

test('registration requires email', function () {
    $this->post('/register', [
        'name' => 'Test User',
        'email' => '',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('email');
});

test('registration rejects invalid email format', function () {
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'not-an-email',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('email');
});

test('registration rejects duplicate email', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'taken@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('email');
});

test('registration requires password', function () {
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => '',
        'password_confirmation' => '',
    ])->assertSessionHasErrors('password');
});

test('registration requires password confirmation to match', function () {
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password1',
        'password_confirmation' => 'password2',
    ])->assertSessionHasErrors('password');
});

test('registration rejects name exceeding 255 characters', function () {
    $this->post('/register', [
        'name' => str_repeat('a', 256),
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors('name');
});

test('registration creates user with correct attributes', function () {
    $this->post('/register', [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'newuser@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->name)->toBe('New User');
    expect($user->password)->not->toBe('password');
});

test('authenticated users are redirected away from registration', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('register'))
        ->assertRedirect(route('dashboard', absolute: false));
});
