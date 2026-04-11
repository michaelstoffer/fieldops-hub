<?php

use App\Models\Customer;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

function techJobSetup(): array
{
    (new RolesAndPermissionsSeeder)->run();

    $org        = Organization::factory()->create();
    $technician = User::factory()->create(['organization_id' => $org->id]);
    $technician->assignRole('technician');
    $customer   = Customer::factory()->create(['organization_id' => $org->id]);

    return [$technician, $org, $customer];
}

// ── Inertia: job list page ────────────────────────────────────────────────────

test('technician job list shows only today\'s assigned jobs', function () {
    [$technician, , $customer] = techJobSetup();

    Job::factory()->forCustomer($customer)->count(2)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
        'status'       => Job::STATUS_SCHEDULED,
    ]);
    // Tomorrow — should not appear
    Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now()->addDay(),
        'status'       => Job::STATUS_SCHEDULED,
    ]);

    $this->actingAs($technician)
        ->get('/technician/jobs')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Technician/Jobs/Index')
            ->has('jobs', 2)
            ->has('statuses')
        );
});

test('technician job list does not include jobs assigned to others', function () {
    [$technician, $org, $customer] = techJobSetup();

    $other = User::factory()->create(['organization_id' => $org->id]);

    Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
        'status'       => Job::STATUS_SCHEDULED,
    ]);
    Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $other->id,
        'scheduled_at' => now(),
        'status'       => Job::STATUS_SCHEDULED,
    ]);

    $this->actingAs($technician)
        ->get('/technician/jobs')
        ->assertInertia(fn ($page) => $page->has('jobs', 1));
});

test('technician job list excludes cancelled jobs', function () {
    [$technician, , $customer] = techJobSetup();

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
        ->get('/technician/jobs')
        ->assertInertia(fn ($page) => $page->has('jobs', 1));
});

// ── Inertia: job show page ────────────────────────────────────────────────────

test('technician can view their own assigned job', function () {
    [$technician, , $customer] = techJobSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->get("/technician/jobs/{$job->id}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Technician/Jobs/Show')
            ->where('job.id', $job->id)
            ->has('statuses')
        );
});

test('technician cannot view a job assigned to another technician', function () {
    [$technician, $org, $customer] = techJobSetup();

    $other = User::factory()->create(['organization_id' => $org->id]);
    $job   = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $other->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->get("/technician/jobs/{$job->id}")
        ->assertForbidden();
});

// ── JSON API: GET /api/technician/jobs/today ──────────────────────────────────

test('api today returns technician\'s jobs for today', function () {
    [$technician, , $customer] = techJobSetup();

    Job::factory()->forCustomer($customer)->count(3)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
        'status'       => Job::STATUS_SCHEDULED,
    ]);

    $this->actingAs($technician)
        ->getJson('/api/technician/jobs/today')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

test('api today excludes jobs not scheduled for today', function () {
    [$technician, , $customer] = techJobSetup();

    Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
        'status'       => Job::STATUS_SCHEDULED,
    ]);
    Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now()->subDay(),
        'status'       => Job::STATUS_SCHEDULED,
    ]);

    $this->actingAs($technician)
        ->getJson('/api/technician/jobs/today')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

test('api today excludes jobs assigned to other technicians', function () {
    [$technician, $org, $customer] = techJobSetup();

    $other = User::factory()->create(['organization_id' => $org->id]);

    Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
        'status'       => Job::STATUS_SCHEDULED,
    ]);
    Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $other->id,
        'scheduled_at' => now(),
        'status'       => Job::STATUS_SCHEDULED,
    ]);

    $this->actingAs($technician)
        ->getJson('/api/technician/jobs/today')
        ->assertJsonCount(1, 'data');
});

test('api today excludes cancelled jobs', function () {
    [$technician, , $customer] = techJobSetup();

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
        ->getJson('/api/technician/jobs/today')
        ->assertJsonCount(1, 'data');
});

test('api today response contains expected job fields', function () {
    [$technician, , $customer] = techJobSetup();

    Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
        'title'        => 'Boiler Inspection',
        'status'       => Job::STATUS_SCHEDULED,
    ]);

    $this->actingAs($technician)
        ->getJson('/api/technician/jobs/today')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'title', 'status', 'scheduled_at', 'customer'],
            ],
        ]);
});

// ── JSON API: GET /api/technician/jobs/{job} ──────────────────────────────────

test('api show returns the technician\'s assigned job', function () {
    [$technician, , $customer] = techJobSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
        'title'        => 'Pipe Fix',
    ]);

    $this->actingAs($technician)
        ->getJson("/api/technician/jobs/{$job->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $job->id)
        ->assertJsonPath('data.title', 'Pipe Fix');
});

test('api show blocks access to another technician\'s job', function () {
    [$technician, $org, $customer] = techJobSetup();

    $other = User::factory()->create(['organization_id' => $org->id]);
    $job   = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $other->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->getJson("/api/technician/jobs/{$job->id}")
        ->assertForbidden();
});

// ── JSON API: PATCH /api/technician/jobs/{job}/status ────────────────────────

test('technician can update job status to en_route', function () {
    [$technician, , $customer] = techJobSetup();

    $job = Job::factory()->forCustomer($customer)->scheduled()->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/status", ['status' => 'en_route'])
        ->assertOk()
        ->assertJsonPath('status', 'ok');

    expect($job->fresh()->status)->toBe(Job::STATUS_EN_ROUTE);
});

test('en_route does not stamp arrived_at', function () {
    [$technician, , $customer] = techJobSetup();

    $job = Job::factory()->forCustomer($customer)->scheduled()->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/status", ['status' => 'en_route'])
        ->assertOk();

    expect($job->fresh()->arrived_at)->toBeNull();
});

test('updating status to in_progress sets both arrived_at and started_at', function () {
    [$technician, , $customer] = techJobSetup();

    $job = Job::factory()->forCustomer($customer)->scheduled()->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/status", ['status' => 'in_progress'])
        ->assertOk();

    $fresh = $job->fresh();
    expect($fresh->arrived_at)->not->toBeNull();
    expect($fresh->started_at)->not->toBeNull();
});

test('in_progress does not overwrite existing arrived_at or started_at', function () {
    [$technician, , $customer] = techJobSetup();

    $arrived = now()->subHour()->startOfSecond();
    $started = now()->subMinutes(45)->startOfSecond();
    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
        'status'       => Job::STATUS_ON_HOLD,
        'arrived_at'   => $arrived,
        'started_at'   => $started,
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/status", ['status' => 'in_progress'])
        ->assertOk();

    $fresh = $job->fresh();
    expect($fresh->arrived_at->equalTo($arrived))->toBeTrue();
    expect($fresh->started_at->equalTo($started))->toBeTrue();
});

test('updating status to completed sets completed_at timestamp', function () {
    [$technician, , $customer] = techJobSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
        'status'       => Job::STATUS_IN_PROGRESS,
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/status", ['status' => 'completed'])
        ->assertOk();

    expect($job->fresh()->completed_at)->not->toBeNull();
    expect($job->fresh()->status)->toBe(Job::STATUS_COMPLETED);
});

test('updating status to on_hold does not set any timestamp', function () {
    [$technician, , $customer] = techJobSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
        'status'       => Job::STATUS_IN_PROGRESS,
        'started_at'   => now()->subMinutes(30),
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/status", ['status' => 'on_hold'])
        ->assertOk();

    expect($job->fresh()->status)->toBe(Job::STATUS_ON_HOLD);
    expect($job->fresh()->completed_at)->toBeNull();
    expect($job->fresh()->cancelled_at)->toBeNull();
});

test('technician cannot set status to cancelled', function () {
    [$technician, , $customer] = techJobSetup();

    $job = Job::factory()->forCustomer($customer)->scheduled()->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/status", ['status' => 'cancelled'])
        ->assertUnprocessable();

    expect($job->fresh()->status)->toBe(Job::STATUS_SCHEDULED);
});

test('status update rejects an invalid status value', function () {
    [$technician, , $customer] = techJobSetup();

    $job = Job::factory()->forCustomer($customer)->scheduled()->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/status", ['status' => 'flying'])
        ->assertUnprocessable();
});

test('technician cannot update status on another technician\'s job', function () {
    [$technician, $org, $customer] = techJobSetup();

    $other = User::factory()->create(['organization_id' => $org->id]);
    $job   = Job::factory()->forCustomer($customer)->scheduled()->create([
        'assigned_to'  => $other->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/status", ['status' => 'en_route'])
        ->assertForbidden();
});

// ── JSON API: PATCH /api/technician/jobs/{job}/notes ─────────────────────────

test('technician can save their notes on a job', function () {
    [$technician, , $customer] = techJobSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/notes", [
            'technician_notes' => 'Replaced capacitor. All working.',
        ])
        ->assertOk()
        ->assertJsonPath('status', 'ok');

    expect($job->fresh()->technician_notes)->toBe('Replaced capacitor. All working.');
});

test('technician can clear their notes by passing null', function () {
    [$technician, , $customer] = techJobSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'      => $technician->id,
        'scheduled_at'     => now(),
        'technician_notes' => 'Old notes',
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/notes", ['technician_notes' => null])
        ->assertOk();

    expect($job->fresh()->technician_notes)->toBeNull();
});

test('technician cannot update notes on another technician\'s job', function () {
    [$technician, $org, $customer] = techJobSetup();

    $other = User::factory()->create(['organization_id' => $org->id]);
    $job   = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $other->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/notes", [
            'technician_notes' => 'Sneaky notes',
        ])
        ->assertForbidden();
});

test('notes update rejects a value exceeding the maximum length', function () {
    [$technician, , $customer] = techJobSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/notes", [
            'technician_notes' => str_repeat('x', 5001),
        ])
        ->assertUnprocessable();
});

// ── Customer notes ─────────────────────────────────────────────────────────────

test('technician can save customer-facing notes on a job', function () {
    [$technician, , $customer] = techJobSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/customer-notes", [
            'customer_notes' => 'Replaced filter. No issues found.',
        ])
        ->assertOk();

    expect($job->fresh()->customer_notes)->toBe('Replaced filter. No issues found.');
});

test('technician can clear customer notes by passing null', function () {
    [$technician, , $customer] = techJobSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'    => $technician->id,
        'scheduled_at'   => now(),
        'customer_notes' => 'Old customer notes',
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/customer-notes", ['customer_notes' => null])
        ->assertOk();

    expect($job->fresh()->customer_notes)->toBeNull();
});

test('technician cannot update customer notes on another technician\'s job', function () {
    [$technician, $org, $customer] = techJobSetup();

    $other = User::factory()->create(['organization_id' => $org->id]);
    $job   = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $other->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/customer-notes", [
            'customer_notes' => 'Sneaky notes',
        ])
        ->assertForbidden();
});

test('customer notes update rejects a value exceeding the maximum length', function () {
    [$technician, , $customer] = techJobSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/customer-notes", [
            'customer_notes' => str_repeat('x', 5001),
        ])
        ->assertUnprocessable();
});
