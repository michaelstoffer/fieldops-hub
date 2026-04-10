<?php

use App\Models\Customer;
use App\Models\Item;
use App\Models\Job;
use App\Models\JobType;
use App\Models\Organization;
use App\Models\Property;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

/**
 * Verifies that every admin resource's edit page returns 404 when the record
 * belongs to a different organization — confirming the org-scoping in
 * getEloquentQuery() is enforced at the HTTP level.
 */
beforeEach(function () {
    (new RolesAndPermissionsSeeder)->run();
});

function scopedOwner(): array
{
    $myOrg    = Organization::factory()->create();
    $user     = User::factory()->create(['organization_id' => $myOrg->id]);
    $user->assignRole('owner');

    $otherOrg = Organization::factory()->create();

    return [$user, $myOrg, $otherOrg];
}

test('customers edit page 404s for other-org record', function () {
    [$user, , $other] = scopedOwner();
    $record = Customer::factory()->create(['organization_id' => $other->id]);

    $this->actingAs($user)->get("/admin/customers/{$record->id}/edit")->assertNotFound();
});

test('properties edit page 404s for other-org record', function () {
    [$user, , $other] = scopedOwner();
    $record = Property::factory()->create(['organization_id' => $other->id]);

    $this->actingAs($user)->get("/admin/properties/{$record->id}/edit")->assertNotFound();
});

test('job-types edit page 404s for other-org record', function () {
    [$user, , $other] = scopedOwner();
    $record = JobType::factory()->create(['organization_id' => $other->id]);

    $this->actingAs($user)->get("/admin/job-types/{$record->id}/edit")->assertNotFound();
});

test('items edit page 404s for other-org record', function () {
    [$user, , $other] = scopedOwner();
    $record = Item::factory()->create(['organization_id' => $other->id]);

    $this->actingAs($user)->get("/admin/items/{$record->id}/edit")->assertNotFound();
});

test('jobs view page 404s for other-org record', function () {
    [$user, , $other] = scopedOwner();
    $customer = Customer::factory()->create(['organization_id' => $other->id]);
    $record   = Job::factory()->forCustomer($customer)->create();

    $this->actingAs($user)->get("/admin/jobs/{$record->id}")->assertNotFound();
});
