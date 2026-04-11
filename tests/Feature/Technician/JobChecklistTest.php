<?php

use App\Models\Customer;
use App\Models\Job;
use App\Models\JobChecklistItem;
use App\Models\JobType;
use App\Models\JobTypeChecklistItem;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

function checklistSetup(): array
{
    (new RolesAndPermissionsSeeder)->run();

    $org        = Organization::factory()->create();
    $technician = User::factory()->create(['organization_id' => $org->id]);
    $technician->assignRole('technician');
    $customer   = Customer::factory()->create(['organization_id' => $org->id]);
    $jobType    = JobType::factory()->create(['organization_id' => $org->id]);

    return [$technician, $org, $customer, $jobType];
}

// ── Template copy-on-create ──────────────────────────────────────────────────

test('creating a job copies job type checklist template onto the job', function () {
    [$technician, , $customer, $jobType] = checklistSetup();

    JobTypeChecklistItem::factory()->for($jobType)->create([
        'label'       => 'Take before photos',
        'sort_order'  => 0,
        'is_required' => true,
    ]);
    JobTypeChecklistItem::factory()->for($jobType)->create([
        'label'       => 'Perform service',
        'sort_order'  => 1,
        'is_required' => false,
    ]);

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to' => $technician->id,
        'job_type_id' => $jobType->id,
        'scheduled_at' => now(),
    ]);

    $items = $job->checklistItems()->orderBy('sort_order')->get();

    expect($items)->toHaveCount(2);
    expect($items[0]->label)->toBe('Take before photos');
    expect($items[0]->is_required)->toBeTrue();
    expect($items[0]->job_type_checklist_item_id)->not->toBeNull();
    expect($items[1]->label)->toBe('Perform service');
    expect($items[1]->is_required)->toBeFalse();
});

test('creating a job without a job type creates no checklist items', function () {
    [$technician, , $customer] = checklistSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'job_type_id'  => null,
        'scheduled_at' => now(),
    ]);

    expect($job->checklistItems()->count())->toBe(0);
});

test('editing the template does not affect checklist on existing jobs', function () {
    [$technician, , $customer, $jobType] = checklistSetup();

    $template = JobTypeChecklistItem::factory()->for($jobType)->create([
        'label' => 'Original label',
    ]);

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'job_type_id'  => $jobType->id,
        'scheduled_at' => now(),
    ]);

    $template->update(['label' => 'Edited label']);

    expect($job->checklistItems()->first()->label)->toBe('Original label');
});

// ── API: checklist appears in show response ─────────────────────────────────

test('api show response includes checklist items', function () {
    [$technician, , $customer, $jobType] = checklistSetup();

    JobTypeChecklistItem::factory()->for($jobType)->count(3)->create();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'job_type_id'  => $jobType->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->getJson("/api/technician/jobs/{$job->id}")
        ->assertOk()
        ->assertJsonCount(3, 'data.checklist_items');
});

// ── API: toggle endpoint ────────────────────────────────────────────────────

test('technician can mark a checklist item complete', function () {
    [$technician, , $customer, $jobType] = checklistSetup();

    JobTypeChecklistItem::factory()->for($jobType)->create();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'job_type_id'  => $jobType->id,
        'scheduled_at' => now(),
    ]);
    $item = $job->checklistItems()->first();

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/checklist/{$item->id}", ['completed' => true])
        ->assertOk()
        ->assertJsonPath('status', 'ok');

    expect($item->fresh()->completed_at)->not->toBeNull();
});

test('technician can mark a checklist item incomplete', function () {
    [$technician, , $customer, $jobType] = checklistSetup();

    JobTypeChecklistItem::factory()->for($jobType)->create();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'job_type_id'  => $jobType->id,
        'scheduled_at' => now(),
    ]);
    $item = $job->checklistItems()->first();
    $item->update(['completed_at' => now()]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/checklist/{$item->id}", ['completed' => false])
        ->assertOk();

    expect($item->fresh()->completed_at)->toBeNull();
});

test('technician cannot toggle a checklist item on another technician\'s job', function () {
    [$technician, $org, $customer, $jobType] = checklistSetup();

    $other = User::factory()->create(['organization_id' => $org->id]);
    JobTypeChecklistItem::factory()->for($jobType)->create();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $other->id,
        'job_type_id'  => $jobType->id,
        'scheduled_at' => now(),
    ]);
    $item = $job->checklistItems()->first();

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/checklist/{$item->id}", ['completed' => true])
        ->assertForbidden();
});

test('toggle returns 404 when item does not belong to the job', function () {
    [$technician, , $customer, $jobType] = checklistSetup();

    JobTypeChecklistItem::factory()->for($jobType)->create();

    $jobA = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'job_type_id'  => $jobType->id,
        'scheduled_at' => now(),
    ]);
    $jobB = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'job_type_id'  => $jobType->id,
        'scheduled_at' => now(),
    ]);

    $itemFromB = $jobB->checklistItems()->first();

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$jobA->id}/checklist/{$itemFromB->id}", ['completed' => true])
        ->assertNotFound();
});

test('toggle rejects a missing completed flag', function () {
    [$technician, , $customer, $jobType] = checklistSetup();

    JobTypeChecklistItem::factory()->for($jobType)->create();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'job_type_id'  => $jobType->id,
        'scheduled_at' => now(),
    ]);
    $item = $job->checklistItems()->first();

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/checklist/{$item->id}", [])
        ->assertUnprocessable();
});
