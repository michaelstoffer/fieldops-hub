<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected from profile settings', function () {
    $this->get(route('profile.edit'))->assertRedirect(route('login'));
});

test('guests are redirected from password settings', function () {
    $this->get(route('user-password.edit'))->assertRedirect(route('login'));
});

test('guests are redirected from appearance settings', function () {
    $this->get(route('appearance.edit'))->assertRedirect(route('login'));
});

test('guests are redirected from two-factor settings', function () {
    $this->get(route('two-factor.show'))->assertRedirect(route('login'));
});

test('settings root redirects to profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings')
        ->assertRedirect(route('profile.edit'));
});

test('appearance page can be rendered', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('appearance.edit'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('settings/Appearance'));
});

test('inertia shared props include auth user for authenticated requests', function () {
    (new RolesAndPermissionsSeeder)->run();
    $user = User::factory()->create();
    $user->assignRole('owner');

    $this->actingAs($user)
        ->get(route('owner.dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->has('auth.user')
            ->where('auth.user.id', $user->id)
            ->where('auth.user.email', $user->email)
        );
});

test('inertia shared props do not expose password', function () {
    (new RolesAndPermissionsSeeder)->run();
    $user = User::factory()->create();
    $user->assignRole('owner');

    $this->actingAs($user)
        ->get(route('owner.dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->missing('auth.user.password')
        );
});

test('inertia shared props do not expose two factor secret', function () {
    (new RolesAndPermissionsSeeder)->run();
    $user = User::factory()->withTwoFactor()->create();
    $user->assignRole('owner');

    $this->actingAs($user)
        ->get(route('owner.dashboard'))
        ->assertInertia(fn (Assert $page) => $page
            ->missing('auth.user.two_factor_secret')
            ->missing('auth.user.two_factor_recovery_codes')
        );
});
