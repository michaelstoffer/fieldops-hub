<?php

use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(fn () => (new RolesAndPermissionsSeeder)->run());

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $this->post('/register', [
        'plan'                  => 'growth',
        'company_name'          => 'Test Company',
        'name'                  => 'Test User',
        'email'                 => 'test@example.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $this->assertAuthenticated();
});

test('registration redirects to owner dashboard', function () {
    $this->post('/register', [
        'plan'                  => 'growth',
        'company_name'          => 'Test Company',
        'name'                  => 'Test User',
        'email'                 => 'test@example.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ])->assertRedirect(route('owner.dashboard'));
});
