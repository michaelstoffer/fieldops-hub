<?php

use App\Models\Customer;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

function adminOwnerUser(): User
{
    (new RolesAndPermissionsSeeder)->run();

    $org  = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');

    return $user;
}

// ── Read-only access ──────────────────────────────────────────────────────────

test('owner can view the admin jobs list', function () {
    $user     = adminOwnerUser();
    $customer = Customer::factory()->create(['organization_id' => $user->organization_id]);
    Job::factory()->forCustomer($customer)->count(2)->create();

    $this->actingAs($user)->get('/admin/jobs')->assertOk();
});

test('owner can view a job detail page', function () {
    $user     = adminOwnerUser();
    $customer = Customer::factory()->create(['organization_id' => $user->organization_id]);
    $job      = Job::factory()->forCustomer($customer)->create();

    $this->actingAs($user)->get("/admin/jobs/{$job->id}")->assertOk();
});

// ── No create/edit routes ──────────────────────────────────────────────────────

test('job create route does not exist in admin panel', function () {
    $this->actingAs(adminOwnerUser())->get('/admin/jobs/create')->assertNotFound();
});

test('job edit route does not exist in admin panel', function () {
    $user     = adminOwnerUser();
    $customer = Customer::factory()->create(['organization_id' => $user->organization_id]);
    $job      = Job::factory()->forCustomer($customer)->create();

    $this->actingAs($user)->get("/admin/jobs/{$job->id}/edit")->assertNotFound();
});

// ── Org scoping ───────────────────────────────────────────────────────────────

test('owner cannot view a job belonging to another organization', function () {
    $user  = adminOwnerUser();
    $other = Organization::factory()->create();
    $otherCustomer = Customer::factory()->create(['organization_id' => $other->id]);
    $job   = Job::factory()->forCustomer($otherCustomer)->create();

    $this->actingAs($user)->get("/admin/jobs/{$job->id}")->assertNotFound();
});
