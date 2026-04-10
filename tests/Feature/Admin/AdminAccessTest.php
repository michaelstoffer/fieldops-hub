<?php

use App\Models\Customer;
use App\Models\Item;
use App\Models\Job;
use App\Models\JobType;
use App\Models\Organization;
use App\Models\Property;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

// Helper: create an org-scoped user and assign a role
function adminTestUser(string $role): User
{
    (new RolesAndPermissionsSeeder)->run();

    $org  = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole($role);

    return $user;
}

// ── Authentication gate ───────────────────────────────────────────────────────

test('unauthenticated users are redirected to admin login', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

test('admin login page renders', function () {
    $this->get('/admin/login')->assertOk();
});

// ── Role-based access: allowed roles ─────────────────────────────────────────

test('admin role can access the admin panel', function () {
    $this->actingAs(adminTestUser('admin'))->get('/admin')->assertOk();
});

test('owner role can access the admin panel', function () {
    $this->actingAs(adminTestUser('owner'))->get('/admin')->assertOk();
});

// ── Role-based access: denied roles ──────────────────────────────────────────

test('dispatcher cannot access the admin panel', function () {
    $this->actingAs(adminTestUser('dispatcher'))->get('/admin')->assertForbidden();
});

test('technician cannot access the admin panel', function () {
    $this->actingAs(adminTestUser('technician'))->get('/admin')->assertForbidden();
});

test('bookkeeper cannot access the admin panel', function () {
    $this->actingAs(adminTestUser('bookkeeper'))->get('/admin')->assertForbidden();
});

test('authenticated user with no role cannot access the admin panel', function () {
    (new RolesAndPermissionsSeeder)->run();
    $user = User::factory()->create();

    $this->actingAs($user)->get('/admin')->assertForbidden();
});
