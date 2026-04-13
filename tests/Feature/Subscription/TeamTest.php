<?php

use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(fn () => (new RolesAndPermissionsSeeder)->run());

/**
 * Helper: create an owner + active trialing subscription for an org.
 */
function ownerWithTrialingOrg(string $plan = 'growth'): array
{
    $org  = Organization::factory()->trialing()->create(['plan' => $plan]);
    $user = User::factory()->owner($org)->create();
    Subscription::factory()->trialing($org, $plan)->create();

    return [$user, $org];
}

// ── index ──────────────────────────────────────────────────────────────────────

test('owner can view team page', function () {
    [$owner] = ownerWithTrialingOrg();

    $this->actingAs($owner)
        ->get(route('owner.team.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Team/Index'));
});

test('team page lists team members', function () {
    [$owner, $org] = ownerWithTrialingOrg();
    $tech = User::factory()->technician($org)->create(['name' => 'Field Tech']);

    $this->actingAs($owner)
        ->get(route('owner.team.index'))
        ->assertInertia(fn ($page) => $page
            ->has('team_members', 2) // owner + technician
        );
});

// ── store ──────────────────────────────────────────────────────────────────────

test('owner can add a technician', function () {
    [$owner, $org] = ownerWithTrialingOrg('growth'); // growth = limit 10

    $this->actingAs($owner)
        ->post(route('owner.team.store'), [
            'name'     => 'New Tech',
            'email'    => 'newtech@example.com',
            'password' => 'Password1!',
            'role'     => 'technician',
        ])
        ->assertRedirect();

    expect(User::where('email', 'newtech@example.com')->first())->not->toBeNull();
});

test('adding a technician assigns the correct role', function () {
    [$owner] = ownerWithTrialingOrg('growth');

    $this->actingAs($owner)
        ->post(route('owner.team.store'), [
            'name'     => 'New Tech',
            'email'    => 'newtech@example.com',
            'password' => 'Password1!',
            'role'     => 'technician',
        ]);

    $user = User::where('email', 'newtech@example.com')->first();
    expect($user->hasRole('technician'))->toBeTrue();
});

test('store is blocked when technician limit is reached', function () {
    // Use an active (paid) starter subscription so the 3-tech cap is enforced.
    // During trial, starter orgs get growth features (10-tech limit), so we need a paid subscription.
    $org  = Organization::factory()->subscribed()->create(['plan' => 'starter']);
    $user = User::factory()->owner($org)->create();
    Subscription::factory()->active($org, 'starter')->create();

    // Fill the cap
    User::factory()->count(3)->technician($org)->create();

    $this->actingAs($user)
        ->post(route('owner.team.store'), [
            'name'     => 'Tech Four',
            'email'    => 'tech4@example.com',
            'password' => 'Password1!',
            'role'     => 'technician',
        ])
        ->assertSessionHasErrors('role');

    expect(User::where('email', 'tech4@example.com')->exists())->toBeFalse();
});

test('store allows non-technician roles when technician cap is reached', function () {
    // Use a paid starter subscription so the 3-tech cap is active
    $org  = Organization::factory()->subscribed()->create(['plan' => 'starter']);
    $user = User::factory()->owner($org)->create();
    Subscription::factory()->active($org, 'starter')->create();

    User::factory()->count(3)->technician($org)->create();

    $this->actingAs($user)
        ->post(route('owner.team.store'), [
            'name'     => 'Dispatcher Dan',
            'email'    => 'dan@example.com',
            'password' => 'Password1!',
            'role'     => 'dispatcher',
        ])
        ->assertRedirect();

    expect(User::where('email', 'dan@example.com')->exists())->toBeTrue();
});

test('store validates required fields', function () {
    [$owner] = ownerWithTrialingOrg();

    $this->actingAs($owner)
        ->post(route('owner.team.store'), [])
        ->assertSessionHasErrors(['name', 'email', 'password', 'role']);
});

test('store rejects duplicate email', function () {
    [$owner] = ownerWithTrialingOrg();
    User::factory()->create(['email' => 'taken@example.com']);

    $this->actingAs($owner)
        ->post(route('owner.team.store'), [
            'name'     => 'Someone',
            'email'    => 'taken@example.com',
            'password' => 'Password1!',
            'role'     => 'dispatcher',
        ])
        ->assertSessionHasErrors('email');
});

// ── update ─────────────────────────────────────────────────────────────────────

test('owner can update a team member role', function () {
    [$owner, $org] = ownerWithTrialingOrg('growth');
    $member = User::factory()->create(['organization_id' => $org->id]);
    $member->assignRole('dispatcher');

    $this->actingAs($owner)
        ->patch(route('owner.team.update', $member), ['role' => 'bookkeeper'])
        ->assertRedirect();

    expect($member->fresh()->hasRole('bookkeeper'))->toBeTrue();
});

test('update blocks promotion to technician when cap is reached', function () {
    // Use an active (paid) starter subscription so the 3-tech cap is enforced.
    $org  = Organization::factory()->subscribed()->create(['plan' => 'starter']);
    $user = User::factory()->owner($org)->create();
    Subscription::factory()->active($org, 'starter')->create();

    User::factory()->count(3)->technician($org)->create();

    $member = User::factory()->create(['organization_id' => $org->id]);
    $member->assignRole('dispatcher');

    $this->actingAs($user)
        ->patch(route('owner.team.update', $member), ['role' => 'technician'])
        ->assertSessionHasErrors('role');

    expect($member->fresh()->hasRole('dispatcher'))->toBeTrue();
});

test('update allows role change within the same non-technician role when cap is reached', function () {
    // Use a paid starter subscription so the 3-tech cap is active
    $org  = Organization::factory()->subscribed()->create(['plan' => 'starter']);
    $user = User::factory()->owner($org)->create();
    Subscription::factory()->active($org, 'starter')->create();

    User::factory()->count(3)->technician($org)->create();

    $member = User::factory()->create(['organization_id' => $org->id]);
    $member->assignRole('dispatcher');

    $this->actingAs($user)
        ->patch(route('owner.team.update', $member), ['role' => 'bookkeeper'])
        ->assertRedirect();

    expect($member->fresh()->hasRole('bookkeeper'))->toBeTrue();
});

test('update rejects users from other organizations', function () {
    [$owner] = ownerWithTrialingOrg();

    $otherOrg  = Organization::factory()->trialing()->create();
    $otherUser = User::factory()->create(['organization_id' => $otherOrg->id]);
    $otherUser->assignRole('dispatcher');

    $this->actingAs($owner)
        ->patch(route('owner.team.update', $otherUser), ['role' => 'technician'])
        ->assertForbidden();
});

// ── destroy ────────────────────────────────────────────────────────────────────

test('owner can remove a team member', function () {
    [$owner, $org] = ownerWithTrialingOrg();
    $member = User::factory()->create(['organization_id' => $org->id]);
    $member->assignRole('dispatcher');

    $this->actingAs($owner)
        ->delete(route('owner.team.destroy', $member))
        ->assertRedirect();

    expect(User::find($member->id))->toBeNull();
});

test('owner cannot remove themselves', function () {
    [$owner] = ownerWithTrialingOrg();

    $this->actingAs($owner)
        ->delete(route('owner.team.destroy', $owner))
        ->assertForbidden();

    expect(User::find($owner->id))->not->toBeNull();
});

test('destroy rejects users from other organizations', function () {
    [$owner] = ownerWithTrialingOrg();

    $otherOrg  = Organization::factory()->trialing()->create();
    $otherUser = User::factory()->create(['organization_id' => $otherOrg->id]);

    $this->actingAs($owner)
        ->delete(route('owner.team.destroy', $otherUser))
        ->assertForbidden();

    expect(User::find($otherUser->id))->not->toBeNull();
});
