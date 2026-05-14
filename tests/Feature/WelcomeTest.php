<?php

use App\Models\User;

test('root redirects authenticated owner to owner dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('owner');

    $this->actingAs($user)->get('/')->assertRedirect(route('owner.dashboard'));
});

test('root redirects authenticated technician to technician dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('technician');

    $this->actingAs($user)->get('/')->assertRedirect(route('technician.dashboard'));
});
