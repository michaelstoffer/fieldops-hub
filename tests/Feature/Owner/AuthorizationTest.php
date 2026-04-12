<?php

use App\Models\Customer;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

function authTestUser(string $role): array
{
    (new RolesAndPermissionsSeeder)->run();
    $org  = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole($role);

    return [$user, $org];
}

// ── Unauthenticated access is blocked ────────────────────────────────────────

test('unauthenticated user cannot access owner area', function () {
    $this->get('/owner/dashboard')->assertRedirect('/login');
    $this->get('/owner/customers')->assertRedirect('/login');
    $this->get('/owner/jobs')->assertRedirect('/login');
});

// ── Technician cannot access the owner area ───────────────────────────────────

test('technician is forbidden from owner dashboard', function () {
    [$user] = authTestUser('technician');
    $this->actingAs($user)->get('/owner/dashboard')->assertForbidden();
});

test('technician is forbidden from owner customers', function () {
    [$user] = authTestUser('technician');
    $this->actingAs($user)->get('/owner/customers')->assertForbidden();
});

test('technician is forbidden from owner jobs', function () {
    [$user] = authTestUser('technician');
    $this->actingAs($user)->get('/owner/jobs')->assertForbidden();
});

test('technician is forbidden from owner billing', function () {
    [$user] = authTestUser('technician');
    $this->actingAs($user)->get('/owner/billing')->assertForbidden();
});

test('technician is forbidden from owner settings', function () {
    [$user] = authTestUser('technician');
    $this->actingAs($user)->get('/owner/settings/company')->assertForbidden();
});

// ── Allowed roles can access owner area ───────────────────────────────────────

test('owner can access owner dashboard', function () {
    [$user] = authTestUser('owner');
    $this->actingAs($user)->get('/owner/dashboard')->assertOk();
});

test('admin can access owner dashboard', function () {
    [$user] = authTestUser('admin');
    $this->actingAs($user)->get('/owner/dashboard')->assertOk();
});

test('dispatcher can access owner dashboard', function () {
    [$user] = authTestUser('dispatcher');
    $this->actingAs($user)->get('/owner/dashboard')->assertOk();
});

test('bookkeeper can access owner dashboard', function () {
    [$user] = authTestUser('bookkeeper');
    $this->actingAs($user)->get('/owner/dashboard')->assertOk();
});

// ── Org scoping: users cannot access other orgs' resources ───────────────────

test('owner cannot view a customer from another organization', function () {
    [$user]        = authTestUser('owner');
    [$otherUser, $otherOrg] = authTestUser('owner');
    $customer = Customer::factory()->create(['organization_id' => $otherOrg->id]);

    $this->actingAs($user)
        ->get("/owner/customers/{$customer->id}")
        ->assertForbidden();
});

test('owner cannot edit a customer from another organization', function () {
    [$user]        = authTestUser('owner');
    [$otherUser, $otherOrg] = authTestUser('owner');
    $customer = Customer::factory()->create(['organization_id' => $otherOrg->id]);

    $this->actingAs($user)
        ->get("/owner/customers/{$customer->id}/edit")
        ->assertForbidden();
});

test('owner cannot delete a customer from another organization', function () {
    [$user]        = authTestUser('owner');
    [$otherUser, $otherOrg] = authTestUser('owner');
    $customer = Customer::factory()->create(['organization_id' => $otherOrg->id]);

    $this->actingAs($user)
        ->delete("/owner/customers/{$customer->id}")
        ->assertForbidden();
});

test('owner cannot view a job from another organization', function () {
    [$user]        = authTestUser('owner');
    [$otherUser, $otherOrg] = authTestUser('owner');
    $customer = Customer::factory()->create(['organization_id' => $otherOrg->id]);
    $job = Job::factory()->forCustomer($customer)->create();

    $this->actingAs($user)
        ->get("/owner/jobs/{$job->id}")
        ->assertForbidden();
});

test('owner cannot update a job from another organization', function () {
    [$user]        = authTestUser('owner');
    [$otherUser, $otherOrg] = authTestUser('owner');
    $customer = Customer::factory()->create(['organization_id' => $otherOrg->id]);
    $job = Job::factory()->forCustomer($customer)->create();

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/status", ['status' => 'completed'])
        ->assertForbidden();
});

test('owner cannot delete a job from another organization', function () {
    [$user]        = authTestUser('owner');
    [$otherUser, $otherOrg] = authTestUser('owner');
    $customer = Customer::factory()->create(['organization_id' => $otherOrg->id]);
    $job = Job::factory()->forCustomer($customer)->create();

    $this->actingAs($user)
        ->delete("/owner/jobs/{$job->id}")
        ->assertForbidden();
});
