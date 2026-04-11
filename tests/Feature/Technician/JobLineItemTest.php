<?php

use App\Models\Customer;
use App\Models\Item;
use App\Models\Job;
use App\Models\JobLineItem;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

function lineItemSetup(): array
{
    (new RolesAndPermissionsSeeder)->run();

    $org        = Organization::factory()->create();
    $technician = User::factory()->create(['organization_id' => $org->id]);
    $technician->assignRole('technician');
    $customer   = Customer::factory()->create(['organization_id' => $org->id]);

    return [$technician, $org, $customer];
}

// ── Add line item ─────────────────────────────────────────────────────────────

test('technician can add a free-text line item', function () {
    [$technician, , $customer] = lineItemSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->postJson("/api/technician/jobs/{$job->id}/line-items", [
            'name'       => 'Labour',
            'unit_price' => 95.00,
            'quantity'   => 2,
        ])
        ->assertCreated()
        ->assertJsonPath('status', 'ok')
        ->assertJsonPath('data.name', 'Labour')
        ->assertJsonPath('data.unit_price', '95.00')
        ->assertJsonPath('data.quantity', '2.000');

    expect($job->lineItems()->count())->toBe(1);
});

test('technician can add a catalog line item and price is snapshotted', function () {
    [$technician, $org, $customer] = lineItemSetup();

    $item = Item::factory()->create([
        'organization_id' => $org->id,
        'name'            => 'Filter Replacement',
        'unit_price'      => 45.00,
    ]);

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->postJson("/api/technician/jobs/{$job->id}/line-items", [
            'item_id'    => $item->id,
            'name'       => $item->name,
            'unit_price' => 45.00,
            'quantity'   => 1,
        ])
        ->assertCreated()
        ->assertJsonPath('data.item_id', $item->id)
        ->assertJsonPath('data.unit_price', '45.00');
});

test('sort_order increments for each new line item', function () {
    [$technician, , $customer] = lineItemSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)->postJson("/api/technician/jobs/{$job->id}/line-items", ['name' => 'A', 'unit_price' => 10, 'quantity' => 1]);
    $this->actingAs($technician)->postJson("/api/technician/jobs/{$job->id}/line-items", ['name' => 'B', 'unit_price' => 20, 'quantity' => 1]);

    $orders = $job->lineItems()->orderBy('sort_order')->pluck('sort_order')->toArray();
    expect($orders)->toBe([1, 2]);
});

test('add line item rejects missing required fields', function () {
    [$technician, , $customer] = lineItemSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->postJson("/api/technician/jobs/{$job->id}/line-items", [
            'unit_price' => 10,
            'quantity'   => 1,
            // missing name
        ])
        ->assertUnprocessable();
});

test('add line item rejects zero quantity', function () {
    [$technician, , $customer] = lineItemSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->postJson("/api/technician/jobs/{$job->id}/line-items", [
            'name'       => 'X',
            'unit_price' => 10,
            'quantity'   => 0,
        ])
        ->assertUnprocessable();
});

test('technician cannot add a line item to another technician\'s job', function () {
    [$technician, $org, $customer] = lineItemSetup();

    $other = User::factory()->create(['organization_id' => $org->id]);
    $job   = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $other->id,
        'scheduled_at' => now(),
    ]);

    $this->actingAs($technician)
        ->postJson("/api/technician/jobs/{$job->id}/line-items", [
            'name'       => 'X',
            'unit_price' => 10,
            'quantity'   => 1,
        ])
        ->assertForbidden();
});

// ── Update line item ──────────────────────────────────────────────────────────

test('technician can update a line item qty and price', function () {
    [$technician, , $customer] = lineItemSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $item = $job->lineItems()->create([
        'name'       => 'Labour',
        'unit_price' => 50.00,
        'quantity'   => 1,
        'sort_order' => 1,
    ]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/line-items/{$item->id}", [
            'quantity'   => 3,
            'unit_price' => 55.00,
        ])
        ->assertOk()
        ->assertJsonPath('data.quantity', '3.000')
        ->assertJsonPath('data.unit_price', '55.00');
});

test('technician can update the name of a line item', function () {
    [$technician, , $customer] = lineItemSetup();

    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'scheduled_at' => now(),
    ]);

    $item = $job->lineItems()->create(['name' => 'Old', 'unit_price' => 10, 'quantity' => 1, 'sort_order' => 1]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/line-items/{$item->id}", ['name' => 'New'])
        ->assertOk()
        ->assertJsonPath('data.name', 'New');
});

test('update returns 404 when line item does not belong to the job', function () {
    [$technician, , $customer] = lineItemSetup();

    $jobA = Job::factory()->forCustomer($customer)->create(['assigned_to' => $technician->id, 'scheduled_at' => now()]);
    $jobB = Job::factory()->forCustomer($customer)->create(['assigned_to' => $technician->id, 'scheduled_at' => now()]);

    $itemFromB = $jobB->lineItems()->create(['name' => 'X', 'unit_price' => 10, 'quantity' => 1, 'sort_order' => 1]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$jobA->id}/line-items/{$itemFromB->id}", ['quantity' => 2])
        ->assertNotFound();
});

test('technician cannot update a line item on another technician\'s job', function () {
    [$technician, $org, $customer] = lineItemSetup();

    $other = User::factory()->create(['organization_id' => $org->id]);
    $job   = Job::factory()->forCustomer($customer)->create(['assigned_to' => $other->id, 'scheduled_at' => now()]);
    $item  = $job->lineItems()->create(['name' => 'X', 'unit_price' => 10, 'quantity' => 1, 'sort_order' => 1]);

    $this->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/line-items/{$item->id}", ['quantity' => 2])
        ->assertForbidden();
});

// ── Delete line item ──────────────────────────────────────────────────────────

test('technician can delete a line item', function () {
    [$technician, , $customer] = lineItemSetup();

    $job  = Job::factory()->forCustomer($customer)->create(['assigned_to' => $technician->id, 'scheduled_at' => now()]);
    $item = $job->lineItems()->create(['name' => 'X', 'unit_price' => 10, 'quantity' => 1, 'sort_order' => 1]);

    $this->actingAs($technician)
        ->deleteJson("/api/technician/jobs/{$job->id}/line-items/{$item->id}")
        ->assertOk()
        ->assertJsonPath('status', 'ok');

    expect($job->lineItems()->count())->toBe(0);
});

test('technician cannot delete a line item from another technician\'s job', function () {
    [$technician, $org, $customer] = lineItemSetup();

    $other = User::factory()->create(['organization_id' => $org->id]);
    $job   = Job::factory()->forCustomer($customer)->create(['assigned_to' => $other->id, 'scheduled_at' => now()]);
    $item  = $job->lineItems()->create(['name' => 'X', 'unit_price' => 10, 'quantity' => 1, 'sort_order' => 1]);

    $this->actingAs($technician)
        ->deleteJson("/api/technician/jobs/{$job->id}/line-items/{$item->id}")
        ->assertForbidden();
});

test('delete returns 404 when item does not belong to the job', function () {
    [$technician, , $customer] = lineItemSetup();

    $jobA    = Job::factory()->forCustomer($customer)->create(['assigned_to' => $technician->id, 'scheduled_at' => now()]);
    $jobB    = Job::factory()->forCustomer($customer)->create(['assigned_to' => $technician->id, 'scheduled_at' => now()]);
    $itemB   = $jobB->lineItems()->create(['name' => 'X', 'unit_price' => 10, 'quantity' => 1, 'sort_order' => 1]);

    $this->actingAs($technician)
        ->deleteJson("/api/technician/jobs/{$jobA->id}/line-items/{$itemB->id}")
        ->assertNotFound();
});

// ── Catalog search ────────────────────────────────────────────────────────────

test('catalog returns active items for the technician\'s org', function () {
    [$technician, $org, $customer] = lineItemSetup();

    Item::factory()->count(3)->create(['organization_id' => $org->id, 'is_active' => true]);
    Item::factory()->create(['organization_id' => $org->id, 'is_active' => false]); // inactive — excluded

    $this->actingAs($technician)
        ->getJson('/api/technician/catalog')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

test('catalog search filters by name', function () {
    [$technician, $org] = lineItemSetup();

    Item::factory()->create(['organization_id' => $org->id, 'name' => 'Filter Replacement', 'is_active' => true]);
    Item::factory()->create(['organization_id' => $org->id, 'name' => 'Pump Repair', 'is_active' => true]);

    $this->actingAs($technician)
        ->getJson('/api/technician/catalog?q=filter')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Filter Replacement');
});

test('catalog does not leak items from other organizations', function () {
    [$technician, $org] = lineItemSetup();

    $otherOrg = Organization::factory()->create();
    Item::factory()->create(['organization_id' => $otherOrg->id, 'is_active' => true]);
    Item::factory()->create(['organization_id' => $org->id, 'is_active' => true]);

    $this->actingAs($technician)
        ->getJson('/api/technician/catalog')
        ->assertOk()
        ->assertJsonCount(1, 'data');
});

// ── Show includes line items ───────────────────────────────────────────────────

test('api show response includes line items', function () {
    [$technician, , $customer] = lineItemSetup();

    $job = Job::factory()->forCustomer($customer)->create(['assigned_to' => $technician->id, 'scheduled_at' => now()]);
    $job->lineItems()->createMany([
        ['name' => 'A', 'unit_price' => 10, 'quantity' => 1, 'sort_order' => 1],
        ['name' => 'B', 'unit_price' => 20, 'quantity' => 2, 'sort_order' => 2],
    ]);

    $this->actingAs($technician)
        ->getJson("/api/technician/jobs/{$job->id}")
        ->assertOk()
        ->assertJsonCount(2, 'data.line_items');
});
