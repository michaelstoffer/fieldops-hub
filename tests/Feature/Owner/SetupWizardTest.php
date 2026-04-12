<?php

use App\Http\Controllers\Owner\SetupController;
use App\Models\JobType;
use App\Models\Organization;
use App\Models\OrganizationSetting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

function setupUser(string $role = 'owner'): array
{
    (new RolesAndPermissionsSeeder)->run();
    $org  = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole($role);

    return [$user, $org];
}

// ── isComplete() logic ────────────────────────────────────────────────────────

test('isComplete returns false when company name is missing', function () {
    (new RolesAndPermissionsSeeder)->run();
    $org = Organization::factory()->create();
    expect(SetupController::isComplete($org->id))->toBeFalse();
});

test('isComplete returns false when no job types exist', function () {
    (new RolesAndPermissionsSeeder)->run();
    $org = Organization::factory()->create();
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'company_name' => 'Acme']);
    expect(SetupController::isComplete($org->id))->toBeFalse();
});

test('isComplete returns false when no technicians exist', function () {
    (new RolesAndPermissionsSeeder)->run();
    $org = Organization::factory()->create();
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'company_name' => 'Acme']);
    JobType::factory()->create(['organization_id' => $org->id]);
    expect(SetupController::isComplete($org->id))->toBeFalse();
});

test('isComplete returns true when all steps are done', function () {
    (new RolesAndPermissionsSeeder)->run();
    $org  = Organization::factory()->create();
    $tech = User::factory()->create(['organization_id' => $org->id]);
    $tech->assignRole('technician');
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'company_name' => 'Acme']);
    JobType::factory()->create(['organization_id' => $org->id]);
    expect(SetupController::isComplete($org->id))->toBeTrue();
});

// ── HTTP routes ───────────────────────────────────────────────────────────────

test('setup wizard is accessible to owner', function () {
    [$user] = setupUser('owner');
    $this->actingAs($user)->get('/owner/setup')->assertOk();
});

test('setup wizard redirects when setup is already complete', function () {
    [$user, $org] = setupUser('owner');
    $tech = User::factory()->create(['organization_id' => $org->id]);
    $tech->assignRole('technician');
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'company_name' => 'Acme']);
    JobType::factory()->create(['organization_id' => $org->id]);

    $this->actingAs($user)->get('/owner/setup')->assertRedirect('/owner/dashboard');
});

test('dispatcher cannot access setup wizard', function () {
    [$user] = setupUser('dispatcher');
    $this->actingAs($user)->get('/owner/setup')->assertForbidden();
});

test('owner can save company info', function () {
    [$user, $org] = setupUser();

    $this->actingAs($user)->post('/owner/setup/company', [
        'name'  => 'My Company',
        'email' => 'info@myco.com',
        'phone' => '555-1234',
    ])->assertRedirect();

    $settings = OrganizationSetting::where('organization_id', $org->id)->first();
    expect($settings->company_name)->toBe('My Company');
    expect($settings->company_email)->toBe('info@myco.com');
});

test('owner can add a job type via setup', function () {
    [$user, $org] = setupUser();

    $this->actingAs($user)->post('/owner/setup/job-types', [
        'name'  => 'HVAC Service',
        'color' => '#3b82f6',
    ])->assertRedirect();

    expect(JobType::where('organization_id', $org->id)->where('name', 'HVAC Service')->exists())->toBeTrue();
});

test('owner can add a technician via setup', function () {
    [$user, $org] = setupUser();

    $this->actingAs($user)->post('/owner/setup/technicians', [
        'name'     => 'Tech Bob',
        'email'    => 'bob@example.com',
        'password' => 'Password1!',
    ])->assertRedirect();

    $tech = User::where('email', 'bob@example.com')->first();
    expect($tech)->not->toBeNull();
    expect($tech->hasRole('technician'))->toBeTrue();
    expect($tech->organization_id)->toBe($org->id);
});

test('complete redirects to dashboard when setup is done', function () {
    [$user, $org] = setupUser();
    $tech = User::factory()->create(['organization_id' => $org->id]);
    $tech->assignRole('technician');
    OrganizationSetting::factory()->create(['organization_id' => $org->id, 'company_name' => 'Acme']);
    JobType::factory()->create(['organization_id' => $org->id]);

    $this->actingAs($user)->post('/owner/setup/complete')->assertRedirect('/owner/dashboard');
});

test('complete returns error when setup is incomplete', function () {
    [$user] = setupUser();

    $this->actingAs($user)->post('/owner/setup/complete')->assertSessionHasErrors('setup');
});
