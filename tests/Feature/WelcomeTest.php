<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('welcome page can be rendered', function () {
    $this->get(route('home'))->assertOk();
});

test('welcome page renders Welcome component', function () {
    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page->component('Welcome'));
});

test('welcome page exposes canRegister prop when registration is enabled', function () {
    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('canRegister')
        );
});

test('authenticated users can still visit welcome page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk();
});
