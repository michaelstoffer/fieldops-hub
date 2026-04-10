<?php

use App\Models\User;

test('profile update requires name', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => '',
            'email' => $user->email,
        ])
        ->assertSessionHasErrors('name');
});

test('profile update requires email', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => '',
        ])
        ->assertSessionHasErrors('email');
});

test('profile update rejects invalid email format', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'not-an-email',
        ])
        ->assertSessionHasErrors('email');
});

test('profile update rejects email already taken by another user', function () {
    $existing = User::factory()->create(['email' => 'taken@example.com']);
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'taken@example.com',
        ])
        ->assertSessionHasErrors('email');
});

test('profile update allows user to keep their own email', function () {
    $user = User::factory()->create(['email' => 'mine@example.com']);

    $this->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'mine@example.com',
        ])
        ->assertSessionHasNoErrors();
});

test('profile update enforces lowercase email', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'UPPER@EXAMPLE.COM',
        ])
        ->assertSessionHasErrors('email');
});

test('profile update rejects name exceeding 255 characters', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => str_repeat('a', 256),
            'email' => $user->email,
        ])
        ->assertSessionHasErrors('name');
});

test('profile update clears email verification when email changes', function () {
    $user = User::factory()->create(['email' => 'old@example.com']);

    $this->actingAs($user)
        ->patch(route('profile.update'), [
            'name' => $user->name,
            'email' => 'new@example.com',
        ]);

    expect($user->fresh()->email_verified_at)->toBeNull();
});

test('unauthenticated profile update is rejected', function () {
    $this->patch(route('profile.update'), [
        'name' => 'Hacker',
        'email' => 'hacker@example.com',
    ])->assertRedirect(route('login'));
});

test('unauthenticated account deletion is rejected', function () {
    $this->delete(route('profile.destroy'), [
        'password' => 'password',
    ])->assertRedirect(route('login'));
});
