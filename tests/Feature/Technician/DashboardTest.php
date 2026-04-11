<?php

use App\Models\Customer;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

function technicianWithOrg(): array
{
    (new RolesAndPermissionsSeeder)->run();

    $org        = Organization::factory()->create();
    $technician = User::factory()->create(['organization_id' => $org->id]);
    $technician->assignRole('technician');
    $customer   = Customer::factory()->create(['organization_id' => $org->id]);

    return [$technician, $org, $customer];
}

// ── today_jobs stat ───────────────────────────────────────────────────────────

test('dashboard shows correct today job count', function () {
    [$technician, , $customer] = technicianWithOrg();

    Job::factory()->forCustomer($customer)->count(3)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
        'status'       => Job::STATUS_SCHEDULED,
    ]);

    $this->actingAs($technician)
        ->get('/technician/dashboard')
        ->assertInertia(fn ($page) => $page
            ->component('Technician/Dashboard')
            ->where('stats.today_jobs', 3)
        );
});

test('dashboard excludes jobs scheduled on other days', function () {
    [$technician, , $customer] = technicianWithOrg();

    // Today
    Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
        'status'       => Job::STATUS_SCHEDULED,
    ]);
    // Yesterday
    Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now()->subDay(),
        'status'       => Job::STATUS_SCHEDULED,
    ]);
    // Tomorrow
    Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now()->addDay(),
        'status'       => Job::STATUS_SCHEDULED,
    ]);

    $this->actingAs($technician)
        ->get('/technician/dashboard')
        ->assertInertia(fn ($page) => $page->where('stats.today_jobs', 1));
});

test('dashboard excludes cancelled jobs from today count', function () {
    [$technician, , $customer] = technicianWithOrg();

    Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
        'status'       => Job::STATUS_SCHEDULED,
    ]);
    Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
        'status'       => Job::STATUS_CANCELLED,
    ]);

    $this->actingAs($technician)
        ->get('/technician/dashboard')
        ->assertInertia(fn ($page) => $page->where('stats.today_jobs', 1));
});

test('dashboard only counts jobs assigned to the authenticated technician', function () {
    [$technician, $org, $customer] = technicianWithOrg();

    $otherTech = User::factory()->create(['organization_id' => $org->id]);

    Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
        'status'       => Job::STATUS_SCHEDULED,
    ]);
    // Job assigned to someone else — should not be counted
    Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $otherTech->id,
        'scheduled_at' => now(),
        'status'       => Job::STATUS_SCHEDULED,
    ]);

    $this->actingAs($technician)
        ->get('/technician/dashboard')
        ->assertInertia(fn ($page) => $page->where('stats.today_jobs', 1));
});

// ── in_progress stat ──────────────────────────────────────────────────────────

test('dashboard shows correct in_progress count', function () {
    [$technician, , $customer] = technicianWithOrg();

    Job::factory()->forCustomer($customer)->count(2)->create([
        'assigned_to' => $technician->id,
        'status'      => Job::STATUS_IN_PROGRESS,
        'scheduled_at' => now()->subHour(),
    ]);
    Job::factory()->forCustomer($customer)->create([
        'assigned_to' => $technician->id,
        'status'      => Job::STATUS_SCHEDULED,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->get('/technician/dashboard')
        ->assertInertia(fn ($page) => $page->where('stats.in_progress', 2));
});

test('dashboard in_progress only counts the authenticated technician\'s jobs', function () {
    [$technician, $org, $customer] = technicianWithOrg();

    $otherTech = User::factory()->create(['organization_id' => $org->id]);

    Job::factory()->forCustomer($customer)->create([
        'assigned_to' => $technician->id,
        'status'      => Job::STATUS_IN_PROGRESS,
        'scheduled_at' => now(),
    ]);
    Job::factory()->forCustomer($customer)->create([
        'assigned_to' => $otherTech->id,
        'status'      => Job::STATUS_IN_PROGRESS,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->get('/technician/dashboard')
        ->assertInertia(fn ($page) => $page->where('stats.in_progress', 1));
});
