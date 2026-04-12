<?php

use App\Models\Customer;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

function userOrgCustomer(): array
{
    (new RolesAndPermissionsSeeder)->run();
    $org      = Organization::factory()->create();
    $user     = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    return [$user, $org, $customer];
}

// ── Index ─────────────────────────────────────────────────────────────────────

test('job index requires authentication', function () {
    $this->get('/owner/jobs')->assertRedirect('/login');
});

test('authenticated user can view their job list', function () {
    [$user, $org, $customer] = userOrgCustomer();
    Job::factory()->forCustomer($customer)->count(3)->create();

    $this->actingAs($user)
        ->get('/owner/jobs')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Owner/Jobs/Index')
            ->has('jobs.data', 3)
        );
});

test('job list is scoped to the authenticated user\'s organization', function () {
    [$user] = userOrgCustomer();
    [, , $otherCustomer] = userOrgCustomer();

    Job::factory()->forCustomer($otherCustomer)->count(5)->create();

    $this->actingAs($user)
        ->get('/owner/jobs')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('jobs.data', 0));
});

test('job list can be filtered by status', function () {
    [$user, $org, $customer] = userOrgCustomer();
    Job::factory()->forCustomer($customer)->scheduled()->count(2)->create();
    Job::factory()->forCustomer($customer)->completed()->count(3)->create();

    $this->actingAs($user)
        ->get('/owner/jobs?status=completed')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('jobs.data', 3));
});

// ── Show ──────────────────────────────────────────────────────────────────────

test('user can view a job that belongs to their organization', function () {
    [$user, $org, $customer] = userOrgCustomer();
    $job = Job::factory()->forCustomer($customer)->create();

    $this->actingAs($user)
        ->get("/owner/jobs/{$job->id}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Jobs/Show'));
});

test('user cannot view a job from another organization', function () {
    [$user] = userOrgCustomer();
    [, , $otherCustomer] = userOrgCustomer();
    $job = Job::factory()->forCustomer($otherCustomer)->create();

    $this->actingAs($user)
        ->get("/owner/jobs/{$job->id}")
        ->assertForbidden();
});

// ── Create / Store ────────────────────────────────────────────────────────────

test('user can view the create job form', function () {
    [$user] = userOrgCustomer();

    $this->actingAs($user)
        ->get('/owner/jobs/create')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Jobs/Create'));
});

test('user can create a job', function () {
    [$user, $org, $customer] = userOrgCustomer();

    $this->actingAs($user)
        ->post('/owner/jobs', [
            'customer_id'  => $customer->id,
            'property_id'  => null,
            'job_type_id'  => null,
            'assigned_to'  => null,
            'title'        => 'AC Service',
            'description'  => null,
            'scheduled_at' => '2026-05-01T09:00',
            'office_notes' => null,
        ])
        ->assertRedirect();

    expect(Job::where('title', 'AC Service')->exists())->toBeTrue();
    expect(Job::where('title', 'AC Service')->first()->organization_id)->toBe($user->organization_id);
    expect(Job::where('title', 'AC Service')->first()->status)->toBe(Job::STATUS_SCHEDULED);
});

test('job creation requires a title and customer', function () {
    [$user] = userOrgCustomer();

    $this->actingAs($user)
        ->post('/owner/jobs', [])
        ->assertSessionHasErrors(['title', 'customer_id']);
});

// ── Edit / Update ─────────────────────────────────────────────────────────────

test('user can view the edit job form', function () {
    [$user, $org, $customer] = userOrgCustomer();
    $job = Job::factory()->forCustomer($customer)->create();

    $this->actingAs($user)
        ->get("/owner/jobs/{$job->id}/edit")
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Jobs/Edit'));
});

test('user cannot edit a job from another organization', function () {
    [$user] = userOrgCustomer();
    [, , $otherCustomer] = userOrgCustomer();
    $job = Job::factory()->forCustomer($otherCustomer)->create();

    $this->actingAs($user)
        ->get("/owner/jobs/{$job->id}/edit")
        ->assertForbidden();
});

test('user can update a job', function () {
    [$user, $org, $customer] = userOrgCustomer();
    $job = Job::factory()->forCustomer($customer)->create();

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}", [
            'customer_id'  => $customer->id,
            'property_id'  => null,
            'job_type_id'  => null,
            'assigned_to'  => null,
            'title'        => 'Updated Title',
            'description'  => null,
            'scheduled_at' => '2026-06-01T10:00',
            'office_notes' => null,
        ])
        ->assertRedirect("/owner/jobs/{$job->id}");

    expect($job->fresh()->title)->toBe('Updated Title');
});

// ── Status ────────────────────────────────────────────────────────────────────

test('user can update job status', function () {
    [$user, $org, $customer] = userOrgCustomer();
    $job = Job::factory()->forCustomer($customer)->scheduled()->create();

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/status", ['status' => 'in_progress'])
        ->assertRedirect();

    expect($job->fresh()->status)->toBe(Job::STATUS_IN_PROGRESS);
    expect($job->fresh()->started_at)->not->toBeNull();
});

test('completing a job sets completed_at timestamp', function () {
    [$user, $org, $customer] = userOrgCustomer();
    $job = Job::factory()->forCustomer($customer)->create(['status' => Job::STATUS_IN_PROGRESS]);

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/status", ['status' => 'completed'])
        ->assertRedirect();

    expect($job->fresh()->completed_at)->not->toBeNull();
});

test('user cannot update status on another org\'s job', function () {
    [$user] = userOrgCustomer();
    [, , $otherCustomer] = userOrgCustomer();
    $job = Job::factory()->forCustomer($otherCustomer)->create();

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/status", ['status' => 'completed'])
        ->assertForbidden();
});

// ── Reschedule ────────────────────────────────────────────────────────────────

test('user can reschedule a job', function () {
    [$user, $org, $customer] = userOrgCustomer();
    $job = Job::factory()->forCustomer($customer)->create(['scheduled_at' => '2026-05-01 09:00:00']);

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/reschedule", ['scheduled_at' => '2026-06-15T10:30'])
        ->assertRedirect();

    expect($job->fresh()->scheduled_at->format('Y-m-d H:i'))->toBe('2026-06-15 10:30');
});

test('reschedule requires a valid date', function () {
    [$user, $org, $customer] = userOrgCustomer();
    $job = Job::factory()->forCustomer($customer)->create();

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/reschedule", ['scheduled_at' => 'not-a-date'])
        ->assertSessionHasErrors(['scheduled_at']);
});

test('reschedule requires scheduled_at', function () {
    [$user, $org, $customer] = userOrgCustomer();
    $job = Job::factory()->forCustomer($customer)->create();

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/reschedule", [])
        ->assertSessionHasErrors(['scheduled_at']);
});

test('user cannot reschedule a job from another organization', function () {
    [$user] = userOrgCustomer();
    [, , $otherCustomer] = userOrgCustomer();
    $job = Job::factory()->forCustomer($otherCustomer)->create();

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/reschedule", ['scheduled_at' => '2026-06-15T10:30'])
        ->assertForbidden();
});

// ── Reassign ──────────────────────────────────────────────────────────────────

test('user can reassign a job to a technician', function () {
    [$user, $org, $customer] = userOrgCustomer();
    $technician = User::factory()->create(['organization_id' => $org->id]);
    $job = Job::factory()->forCustomer($customer)->create(['assigned_to' => null]);

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/reassign", ['assigned_to' => $technician->id])
        ->assertRedirect();

    expect($job->fresh()->assigned_to)->toBe($technician->id);
});

test('user can unassign a technician by passing null', function () {
    [$user, $org, $customer] = userOrgCustomer();
    $technician = User::factory()->create(['organization_id' => $org->id]);
    $job = Job::factory()->forCustomer($customer)->create(['assigned_to' => $technician->id]);

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/reassign", ['assigned_to' => null])
        ->assertRedirect();

    expect($job->fresh()->assigned_to)->toBeNull();
});

test('reassign rejects a non-existent user id', function () {
    [$user, $org, $customer] = userOrgCustomer();
    $job = Job::factory()->forCustomer($customer)->create();

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/reassign", ['assigned_to' => 999999])
        ->assertSessionHasErrors(['assigned_to']);
});

test('user cannot reassign a job from another organization', function () {
    [$user] = userOrgCustomer();
    [, , $otherCustomer] = userOrgCustomer();
    $job = Job::factory()->forCustomer($otherCustomer)->create();

    $this->actingAs($user)
        ->patch("/owner/jobs/{$job->id}/reassign", ['assigned_to' => null])
        ->assertForbidden();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

test('user can soft-delete their job', function () {
    [$user, $org, $customer] = userOrgCustomer();
    $job = Job::factory()->forCustomer($customer)->create();

    $this->actingAs($user)
        ->delete("/owner/jobs/{$job->id}")
        ->assertRedirect('/owner/jobs');

    expect(Job::find($job->id))->toBeNull();
    expect(Job::withTrashed()->find($job->id))->not->toBeNull();
});

test('user cannot delete a job from another organization', function () {
    [$user] = userOrgCustomer();
    [, , $otherCustomer] = userOrgCustomer();
    $job = Job::factory()->forCustomer($otherCustomer)->create();

    $this->actingAs($user)
        ->delete("/owner/jobs/{$job->id}")
        ->assertForbidden();
});
