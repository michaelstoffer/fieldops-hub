<?php

use App\Models\JobType;
use App\Models\Organization;
use App\Models\User;

function jobTypeUser(): array
{
    $org  = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');

    return [$user, $org];
}

// ── Index ──────────────────────────────────────────────────────────────────────

test('job type index requires authentication', function () {
    $this->get('/owner/job-types')->assertRedirect('/login');
});

test('user can view their job type list', function () {
    [$user, $org] = jobTypeUser();
    JobType::factory()->count(3)->create(['organization_id' => $org->id]);

    $this->actingAs($user)
        ->get('/owner/job-types')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Owner/JobTypes/Index')
            ->has('jobTypes', 3)
        );
});

test('job type list is scoped to organization', function () {
    [$user] = jobTypeUser();
    [, $otherOrg] = jobTypeUser();

    JobType::factory()->count(4)->create(['organization_id' => $otherOrg->id]);

    $this->actingAs($user)
        ->get('/owner/job-types')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('jobTypes', 0));
});

// ── Create / Store ─────────────────────────────────────────────────────────────

test('user can view the create job type form', function () {
    [$user] = jobTypeUser();

    $this->actingAs($user)
        ->get('/owner/job-types/create')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/JobTypes/Create'));
});

test('user can create a job type', function () {
    [$user, $org] = jobTypeUser();

    $this->actingAs($user)
        ->post('/owner/job-types', [
            'name'        => 'HVAC Service',
            'color'       => '#3b82f6',
            'description' => 'Heating and cooling services',
            'is_active'   => true,
        ])
        ->assertRedirect('/owner/job-types');

    $type = JobType::where('name', 'HVAC Service')->first();
    expect($type)->not->toBeNull();
    expect($type->organization_id)->toBe($org->id);
    expect($type->is_active)->toBeTrue();
});

test('job type creation is scoped to the authenticated user\'s organization', function () {
    [$user] = jobTypeUser();

    $this->actingAs($user)
        ->post('/owner/job-types', ['name' => 'Plumbing', 'color' => '#ef4444', 'is_active' => true]);

    expect(JobType::where('name', 'Plumbing')->first()->organization_id)
        ->toBe($user->organization_id);
});

test('job type creation requires name and color', function () {
    [$user] = jobTypeUser();

    $this->actingAs($user)
        ->post('/owner/job-types', [])
        ->assertSessionHasErrors(['name', 'color']);
});

test('job type name must not exceed 255 characters', function () {
    [$user] = jobTypeUser();

    $this->actingAs($user)
        ->post('/owner/job-types', ['name' => str_repeat('x', 256), 'color' => '#000000'])
        ->assertSessionHasErrors('name');
});

// ── Edit / Update ──────────────────────────────────────────────────────────────

test('user can view the edit form for their job type', function () {
    [$user, $org] = jobTypeUser();
    $type = JobType::factory()->create(['organization_id' => $org->id]);

    $this->actingAs($user)
        ->get("/owner/job-types/{$type->id}/edit")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Owner/JobTypes/Edit')
            ->where('jobType.id', $type->id)
        );
});

test('user cannot view the edit form for another org\'s job type', function () {
    [$user] = jobTypeUser();
    [, $otherOrg] = jobTypeUser();
    $type = JobType::factory()->create(['organization_id' => $otherOrg->id]);

    $this->actingAs($user)
        ->get("/owner/job-types/{$type->id}/edit")
        ->assertForbidden();
});

test('user can update their job type', function () {
    [$user, $org] = jobTypeUser();
    $type = JobType::factory()->create(['organization_id' => $org->id, 'name' => 'Old Name']);

    $this->actingAs($user)
        ->patch("/owner/job-types/{$type->id}", [
            'name'      => 'New Name',
            'color'     => '#10b981',
            'is_active' => true,
        ])
        ->assertRedirect('/owner/job-types');

    expect($type->fresh()->name)->toBe('New Name');
    expect($type->fresh()->color)->toBe('#10b981');
});

test('user cannot update another org\'s job type', function () {
    [$user] = jobTypeUser();
    [, $otherOrg] = jobTypeUser();
    $type = JobType::factory()->create(['organization_id' => $otherOrg->id]);

    $this->actingAs($user)
        ->patch("/owner/job-types/{$type->id}", ['name' => 'Hijacked', 'color' => '#000', 'is_active' => true])
        ->assertForbidden();
});

test('job type update requires name and color', function () {
    [$user, $org] = jobTypeUser();
    $type = JobType::factory()->create(['organization_id' => $org->id]);

    $this->actingAs($user)
        ->patch("/owner/job-types/{$type->id}", [])
        ->assertSessionHasErrors(['name', 'color']);
});

// ── Deactivate (Destroy) ───────────────────────────────────────────────────────

test('user can deactivate their job type', function () {
    [$user, $org] = jobTypeUser();
    $type = JobType::factory()->create(['organization_id' => $org->id, 'is_active' => true]);

    $this->actingAs($user)
        ->delete("/owner/job-types/{$type->id}")
        ->assertRedirect('/owner/job-types');

    expect($type->fresh()->is_active)->toBeFalse();
    // soft-deactivation, not hard-delete
    expect(JobType::find($type->id))->not->toBeNull();
});

test('user cannot deactivate another org\'s job type', function () {
    [$user] = jobTypeUser();
    [, $otherOrg] = jobTypeUser();
    $type = JobType::factory()->create(['organization_id' => $otherOrg->id]);

    $this->actingAs($user)
        ->delete("/owner/job-types/{$type->id}")
        ->assertForbidden();

    expect($type->fresh()->is_active)->toBeTrue();
});

// ── Quick-create (JSON endpoint) ──────────────────────────────────────────────

test('quick-create returns the new job type as JSON', function () {
    [$user, $org] = jobTypeUser();

    $response = $this->actingAs($user)
        ->postJson('/owner/job-types/quick-create', [
            'name'  => 'Pest Control',
            'color' => '#ef4444',
        ]);

    $response->assertCreated()
        ->assertJsonStructure(['id', 'name', 'color']);

    $type = JobType::where('name', 'Pest Control')->first();
    expect($type)->not->toBeNull();
    expect($type->organization_id)->toBe($org->id);
    expect($type->is_active)->toBeTrue();
});

test('quick-create job type is scoped to authenticated user\'s organization', function () {
    [$user] = jobTypeUser();

    $this->actingAs($user)
        ->postJson('/owner/job-types/quick-create', [
            'name'  => 'Landscaping',
            'color' => '#10b981',
        ]);

    expect(JobType::where('name', 'Landscaping')->first()->organization_id)
        ->toBe($user->organization_id);
});

test('quick-create job type requires name and color', function () {
    [$user] = jobTypeUser();

    $this->actingAs($user)
        ->postJson('/owner/job-types/quick-create', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'color']);
});

test('quick-create job type requires authentication', function () {
    $this->postJson('/owner/job-types/quick-create', ['name' => 'Test', 'color' => '#000'])
        ->assertUnauthorized();
});
