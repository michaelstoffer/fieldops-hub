<?php

use App\Models\Customer;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

// Helper: create a user with a given role in their own org
function techUser(string $role): array
{
    (new RolesAndPermissionsSeeder)->run();

    $org  = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole($role);

    return [$user, $org];
}

// ── Unauthenticated access ────────────────────────────────────────────────────

test('unauthenticated user is redirected from technician dashboard', function () {
    $this->get('/technician/dashboard')->assertRedirect('/login');
});

test('unauthenticated user is redirected from technician jobs list', function () {
    $this->get('/technician/jobs')->assertRedirect('/login');
});

test('unauthenticated user is blocked from api jobs today', function () {
    $this->getJson('/api/technician/jobs/today')->assertUnauthorized();
});

// ── Role gate: non-technician roles are blocked ───────────────────────────────

test('owner role cannot access technician dashboard', function () {
    [$user] = techUser('owner');
    $this->actingAs($user)->get('/technician/dashboard')->assertForbidden();
});

test('dispatcher role cannot access technician dashboard', function () {
    [$user] = techUser('dispatcher');
    $this->actingAs($user)->get('/technician/dashboard')->assertForbidden();
});

test('bookkeeper role cannot access technician dashboard', function () {
    [$user] = techUser('bookkeeper');
    $this->actingAs($user)->get('/technician/dashboard')->assertForbidden();
});

test('user with no role cannot access technician dashboard', function () {
    (new RolesAndPermissionsSeeder)->run();
    $user = User::factory()->create();
    $this->actingAs($user)->get('/technician/dashboard')->assertForbidden();
});

test('owner role cannot access technician api', function () {
    [$user] = techUser('owner');
    $this->actingAs($user)->getJson('/api/technician/jobs/today')->assertForbidden();
});

// ── Role gate: technician role is allowed ─────────────────────────────────────

test('technician can access the dashboard page', function () {
    [$user] = techUser('technician');

    $this->actingAs($user)
        ->get('/technician/dashboard')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Technician/Dashboard'));
});

test('technician can access the jobs list page', function () {
    [$user, $org] = techUser('technician');

    $this->actingAs($user)
        ->get('/technician/jobs')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Technician/Jobs/Index'));
});

// ── Post-login redirect ───────────────────────────────────────────────────────

test('technician is redirected to technician dashboard after login', function () {
    [$user] = techUser('technician');

    $this->post('/login', [
        'email'    => $user->email,
        'password' => 'password',
    ])->assertRedirect('/technician/dashboard');
});

test('owner is redirected to owner dashboard after login', function () {
    [$user] = techUser('owner');

    $this->post('/login', [
        'email'    => $user->email,
        'password' => 'password',
    ])->assertRedirect('/owner/dashboard');
});

test('dispatcher is redirected to owner dashboard after login', function () {
    [$user] = techUser('dispatcher');

    $this->post('/login', [
        'email'    => $user->email,
        'password' => 'password',
    ])->assertRedirect('/owner/dashboard');
});

// ── Auth roles shared with Inertia ────────────────────────────────────────────

test('authenticated technician has roles in inertia shared props', function () {
    [$user] = techUser('technician');

    $this->actingAs($user)
        ->get('/technician/dashboard')
        ->assertInertia(fn ($page) => $page
            ->where('auth.roles.0', 'technician')
        );
});
