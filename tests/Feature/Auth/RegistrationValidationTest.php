<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(fn () => (new RolesAndPermissionsSeeder)->run());

$validData = fn (array $overrides = []) => array_merge([
    'plan'                  => 'growth',
    'company_name'          => 'Test Company',
    'name'                  => 'Test User',
    'email'                 => 'test@example.com',
    'password'              => 'Password1!',
    'password_confirmation' => 'Password1!',
], $overrides);

test('registration requires name', function () use ($validData) {
    $this->post('/register', $validData(['name' => '']))
        ->assertSessionHasErrors('name');
});

test('registration requires email', function () use ($validData) {
    $this->post('/register', $validData(['email' => '']))
        ->assertSessionHasErrors('email');
});

test('registration rejects invalid email format', function () use ($validData) {
    $this->post('/register', $validData(['email' => 'not-an-email']))
        ->assertSessionHasErrors('email');
});

test('registration rejects duplicate email', function () use ($validData) {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->post('/register', $validData(['email' => 'taken@example.com']))
        ->assertSessionHasErrors('email');
});

test('registration requires password', function () use ($validData) {
    $this->post('/register', $validData([
        'password'              => '',
        'password_confirmation' => '',
    ]))->assertSessionHasErrors('password');
});

test('registration requires password confirmation to match', function () use ($validData) {
    $this->post('/register', $validData([
        'password'              => 'Password1!',
        'password_confirmation' => 'DifferentPass1!',
    ]))->assertSessionHasErrors('password');
});

test('registration rejects name exceeding 255 characters', function () use ($validData) {
    $this->post('/register', $validData(['name' => str_repeat('a', 256)]))
        ->assertSessionHasErrors('name');
});

test('registration creates user with correct attributes', function () use ($validData) {
    $this->post('/register', $validData([
        'name'  => 'New User',
        'email' => 'newuser@example.com',
    ]));

    $user = User::where('email', 'newuser@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->name)->toBe('New User');
    expect($user->password)->not->toBe('Password1!');
});

test('authenticated users are redirected away from registration', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('register'))
        ->assertRedirect(route('dashboard', absolute: false));
});
