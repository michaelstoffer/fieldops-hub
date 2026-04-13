<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

test('root returns the marketing page for guests', function () {
    $this->get('/')->assertOk();
});

test('root redirects authenticated owner to owner dashboard', function () {
    (new RolesAndPermissionsSeeder)->run();
    $user = User::factory()->create();
    $user->assignRole('owner');

    $this->actingAs($user)->get('/')->assertRedirect(route('owner.dashboard'));
});

test('root redirects authenticated technician to technician dashboard', function () {
    (new RolesAndPermissionsSeeder)->run();
    $user = User::factory()->create();
    $user->assignRole('technician');

    $this->actingAs($user)->get('/')->assertRedirect(route('technician.dashboard'));
});
