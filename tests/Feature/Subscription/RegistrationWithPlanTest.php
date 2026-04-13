<?php

use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;
use App\Services\PlanService;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(fn () => (new RolesAndPermissionsSeeder)->run());

// ── Plan validation ───────────────────────────────────────────────────────────

test('registration requires a plan', function () {
    $this->post('/register', [
        'company_name'          => 'Acme HVAC',
        'name'                  => 'Jane Smith',
        'email'                 => 'jane@acme.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ])->assertSessionHasErrors('plan');
});

test('registration rejects an invalid plan', function () {
    $this->post('/register', [
        'plan'                  => 'enterprise',
        'company_name'          => 'Acme HVAC',
        'name'                  => 'Jane Smith',
        'email'                 => 'jane@acme.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ])->assertSessionHasErrors('plan');
});

test('registration requires company_name', function () {
    $this->post('/register', [
        'plan'                  => 'growth',
        'company_name'          => '',
        'name'                  => 'Jane Smith',
        'email'                 => 'jane@acme.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ])->assertSessionHasErrors('company_name');
});

// ── Happy path ────────────────────────────────────────────────────────────────

test('registration with growth plan creates organization and trial subscription', function () {
    $this->post('/register', [
        'plan'                  => 'growth',
        'company_name'          => 'Acme HVAC',
        'name'                  => 'Jane Smith',
        'email'                 => 'jane@acme.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $user = User::where('email', 'jane@acme.com')->first();
    expect($user)->not->toBeNull();

    $org = $user->organization;
    expect($org)->not->toBeNull();
    expect($org->name)->toBe('Acme HVAC');
    expect($org->plan)->toBe('growth');
    expect($org->trial_ends_at)->not->toBeNull();
    expect($org->trial_ends_at->isFuture())->toBeTrue();

    $subscription = $org->activeSubscription();
    expect($subscription)->not->toBeNull();
    expect($subscription->status)->toBe(Subscription::STATUS_TRIALING);
    expect($subscription->plan)->toBe('growth');
});

test('registration with starter plan stores starter on org', function () {
    $this->post('/register', [
        'plan'                  => 'starter',
        'company_name'          => 'Small Crew LLC',
        'name'                  => 'Bob Jones',
        'email'                 => 'bob@smallcrew.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $org = User::where('email', 'bob@smallcrew.com')->first()->organization;
    expect($org->plan)->toBe('starter');
});

test('registration with pro plan stores pro on org', function () {
    $this->post('/register', [
        'plan'                  => 'pro',
        'company_name'          => 'Big Ops Inc',
        'name'                  => 'Alice Green',
        'email'                 => 'alice@bigops.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $org = User::where('email', 'alice@bigops.com')->first()->organization;
    expect($org->plan)->toBe('pro');
});

test('registration assigns owner role to registering user', function () {
    $this->post('/register', [
        'plan'                  => 'growth',
        'company_name'          => 'Acme HVAC',
        'name'                  => 'Jane Smith',
        'email'                 => 'jane@acme.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $user = User::where('email', 'jane@acme.com')->first();
    expect($user->hasRole('owner'))->toBeTrue();
});

test('trial period is exactly 14 days', function () {
    $this->post('/register', [
        'plan'                  => 'growth',
        'company_name'          => 'Acme HVAC',
        'name'                  => 'Jane Smith',
        'email'                 => 'jane@acme.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $org = User::where('email', 'jane@acme.com')->first()->organization;
    $daysUntilExpiry = (int) round(now()->diffInDays($org->trial_ends_at));

    expect($daysUntilExpiry)->toBe(PlanService::TRIAL_DAYS);
});

test('registration slug is unique even when company names collide', function () {
    // Pre-create an org with the same slug base
    Organization::factory()->create(['name' => 'Acme HVAC', 'slug' => 'acme-hvac']);

    $this->post('/register', [
        'plan'                  => 'growth',
        'company_name'          => 'Acme HVAC',
        'name'                  => 'Jane Smith',
        'email'                 => 'jane@acme.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $user = User::where('email', 'jane@acme.com')->first();
    expect($user->organization->slug)->not->toBe('acme-hvac');
    expect(Organization::where('slug', $user->organization->slug)->count())->toBe(1);
});

test('registration redirects authenticated user to owner dashboard', function () {
    $this->post('/register', [
        'plan'                  => 'growth',
        'company_name'          => 'Acme HVAC',
        'name'                  => 'Jane Smith',
        'email'                 => 'jane@acme.com',
        'password'              => 'Password1!',
        'password_confirmation' => 'Password1!',
    ])->assertRedirect(route('owner.dashboard'));
});
