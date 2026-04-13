<?php

use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(fn () => (new RolesAndPermissionsSeeder)->run());

// ── Access allowed during active trial ────────────────────────────────────────

test('owner with active trial can access subscription-gated routes', function () {
    $org  = Organization::factory()->trialing()->create();
    $user = User::factory()->owner($org)->create();

    Subscription::factory()->trialing($org)->create();

    $this->actingAs($user)
        ->get(route('owner.team.index'))
        ->assertOk();
});

test('owner with active paid subscription can access subscription-gated routes', function () {
    $org  = Organization::factory()->subscribed()->create();
    $user = User::factory()->owner($org)->create();

    Subscription::factory()->active($org)->create();

    $this->actingAs($user)
        ->get(route('owner.team.index'))
        ->assertOk();
});

// ── Access blocked when trial expired ────────────────────────────────────────

test('owner with expired trial is redirected to expired page', function () {
    $org  = Organization::factory()->trialExpired()->create();
    $user = User::factory()->owner($org)->create();

    Subscription::factory()->trialExpired($org)->create();

    $this->actingAs($user)
        ->get(route('owner.team.index'))
        ->assertRedirect(route('owner.subscription.expired'));
});

test('owner with no subscription is redirected to expired page', function () {
    $org  = Organization::factory()->withoutSubscription()->create(['plan' => 'growth']);
    $user = User::factory()->owner($org)->create();

    $this->actingAs($user)
        ->get(route('owner.team.index'))
        ->assertRedirect(route('owner.subscription.expired'));
});

test('owner with canceled subscription is redirected to expired page', function () {
    $org  = Organization::factory()->withoutSubscription()->create(['plan' => 'growth']);
    $user = User::factory()->owner($org)->create();

    Subscription::factory()->canceled($org)->create();

    $this->actingAs($user)
        ->get(route('owner.team.index'))
        ->assertRedirect(route('owner.subscription.expired'));
});

// ── Non-owner roles bypass subscription check ─────────────────────────────────

test('technician is not subject to subscription check', function () {
    $org  = Organization::factory()->create(['plan' => 'growth']);
    $user = User::factory()->technician($org)->create();
    // No subscription — technicians always pass through

    $this->actingAs($user)
        ->get(route('technician.dashboard'))
        ->assertOk();
});

// ── Expired page is accessible without subscription ───────────────────────────

test('expired page is accessible even with expired trial', function () {
    $org  = Organization::factory()->trialExpired()->create();
    $user = User::factory()->owner($org)->create();

    Subscription::factory()->trialExpired($org)->create();

    $this->actingAs($user)
        ->get(route('owner.subscription.expired'))
        ->assertOk();
});
