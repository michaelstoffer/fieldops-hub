<?php

/**
 * End-to-end feature tests covering the core FieldOps Hub workflows:
 *  1. Full job lifecycle: creation → assignment → en-route → completion
 *  2. Invoicing: generate from job → send → collect payment (full)
 *  3. Partial payment + second payment → paid
 *  4. Estimate → acceptance → convert to job → complete → invoice
 *  5. Technician job workflow via PWA API
 */

use App\Models\Customer;
use App\Models\Estimate;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\JobType;
use App\Models\Organization;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

// ── Helpers ───────────────────────────────────────────────────────────────────

function e2eSetup(): array
{
    (new RolesAndPermissionsSeeder)->run();

    $org        = Organization::factory()->create();
    $owner      = User::factory()->create(['organization_id' => $org->id]);
    $owner->assignRole('owner');
    $technician = User::factory()->create(['organization_id' => $org->id]);
    $technician->assignRole('technician');
    $customer   = Customer::factory()->create(['organization_id' => $org->id, 'email' => 'client@example.com']);

    return [$owner, $technician, $org, $customer];
}

// ── 1. Full job lifecycle ─────────────────────────────────────────────────────

test('full job lifecycle: create → assign → en-route → complete', function () {
    [$owner, $technician, $org, $customer] = e2eSetup();

    // Step 1: Create job
    $response = test()->actingAs($owner)->post('/owner/jobs', [
        'customer_id'  => $customer->id,
        'title'        => 'Full Lifecycle Job',
        'scheduled_at' => '2026-07-01T09:00',
    ]);
    $response->assertRedirect();
    $job = Job::where('title', 'Full Lifecycle Job')->firstOrFail();
    expect($job->status)->toBe(Job::STATUS_SCHEDULED);
    expect($job->organization_id)->toBe($org->id);

    // Step 2: Assign technician
    test()->actingAs($owner)
        ->patch("/owner/jobs/{$job->id}/reassign", ['assigned_to' => $technician->id])
        ->assertRedirect();
    expect($job->fresh()->assigned_to)->toBe($technician->id);

    // Step 3: Technician marks en-route via API
    test()->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/status", ['status' => Job::STATUS_EN_ROUTE])
        ->assertOk();
    expect($job->fresh()->status)->toBe(Job::STATUS_EN_ROUTE);

    // Step 4: Technician marks in-progress
    test()->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/status", ['status' => Job::STATUS_IN_PROGRESS])
        ->assertOk();
    expect($job->fresh()->status)->toBe(Job::STATUS_IN_PROGRESS);

    // Step 5: Technician completes job
    test()->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/status", ['status' => Job::STATUS_COMPLETED])
        ->assertOk();

    $completed = $job->fresh();
    expect($completed->status)->toBe(Job::STATUS_COMPLETED);
    expect($completed->completed_at)->not->toBeNull();
});

// ── 2. Full invoicing flow ────────────────────────────────────────────────────

test('invoicing flow: generate from completed job → send → record full payment', function () {
    [$owner, $technician, $org, $customer] = e2eSetup();

    $job = Job::factory()->forCustomer($customer)->completed()->create(['title' => 'E2E Invoice Job']);
    $job->lineItems()->create(['name' => 'Labor', 'unit_price' => 200, 'quantity' => 1, 'sort_order' => 0]);
    $job->lineItems()->create(['name' => 'Parts', 'unit_price' => 50,  'quantity' => 2, 'sort_order' => 1]);

    // Generate invoice
    test()->actingAs($owner)
        ->post("/owner/jobs/{$job->id}/invoice")
        ->assertRedirect();

    $invoice = $job->fresh()->invoice;
    expect($invoice)->not->toBeNull();
    expect($invoice->status)->toBe(Invoice::STATUS_DRAFT);
    expect((float) $invoice->total)->toBe(300.0);

    // Send invoice
    test()->actingAs($owner)
        ->post("/owner/invoices/{$invoice->id}/send")
        ->assertRedirect();
    expect($invoice->fresh()->status)->toBe(Invoice::STATUS_SENT);

    // Record full payment
    $total = (float) $invoice->total;
    test()->actingAs($owner)->post("/owner/invoices/{$invoice->id}/payments", [
        'amount'  => $total,
        'method'  => 'cash',
        'paid_at' => today()->toDateString(),
    ])->assertRedirect();

    $invoice->refresh();
    expect($invoice->status)->toBe(Invoice::STATUS_PAID);
    expect((float) $invoice->amount_paid)->toBe($total);
    expect((float) $invoice->balance_due)->toBe(0.0);
    expect(Payment::where('invoice_id', $invoice->id)->count())->toBe(1);
});

// ── 3. Partial payment + follow-up payment ────────────────────────────────────

test('partial payment followed by second payment marks invoice paid', function () {
    [$owner] = e2eSetup();
    $customer = Customer::where('organization_id', $owner->organization_id)->first();

    $job = Job::factory()->forCustomer($customer)->completed()->create();
    $job->lineItems()->create(['name' => 'Service', 'unit_price' => 500, 'quantity' => 1, 'sort_order' => 0]);

    test()->actingAs($owner)->post("/owner/jobs/{$job->id}/invoice")->assertRedirect();
    $invoice = $job->fresh()->invoice;

    test()->actingAs($owner)->post("/owner/invoices/{$invoice->id}/send")->assertRedirect();

    // First partial payment
    test()->actingAs($owner)->post("/owner/invoices/{$invoice->id}/payments", [
        'amount'  => 200.00,
        'method'  => 'check',
        'paid_at' => today()->toDateString(),
    ])->assertRedirect();

    $invoice->refresh();
    expect($invoice->status)->toBe(Invoice::STATUS_PARTIAL);
    expect((float) $invoice->balance_due)->toBe(300.0);

    // Second payment clears balance
    test()->actingAs($owner)->post("/owner/invoices/{$invoice->id}/payments", [
        'amount'  => 300.00,
        'method'  => 'card',
        'paid_at' => today()->toDateString(),
    ])->assertRedirect();

    $invoice->refresh();
    expect($invoice->status)->toBe(Invoice::STATUS_PAID);
    expect((float) $invoice->balance_due)->toBe(0.0);
    expect(Payment::where('invoice_id', $invoice->id)->count())->toBe(2);
});

// ── 4. Estimate → convert to job → complete → invoice ────────────────────────

test('estimate accepted and converted to job, then completed and invoiced', function () {
    [$owner] = e2eSetup();
    $customer = Customer::where('organization_id', $owner->organization_id)->first();

    // Create estimate
    test()->actingAs($owner)->post('/owner/estimates', [
        'customer_id' => $customer->id,
        'title'       => 'E2E Estimate',
        'packages'    => [
            [
                'tier'        => 'good',
                'label'       => 'Standard',
                'description' => null,
                'line_items'  => [
                    ['name' => 'Installation', 'unit_price' => 400, 'quantity' => 1],
                ],
            ],
        ],
    ])->assertRedirect();

    $estimate = Estimate::where('title', 'E2E Estimate')->firstOrFail();

    // Simulate customer acceptance
    $estimate->update(['status' => Estimate::STATUS_ACCEPTED, 'accepted_package_id' => $estimate->packages->first()->id]);

    // Convert to job
    test()->actingAs($owner)
        ->post("/owner/estimates/{$estimate->id}/convert")
        ->assertRedirect();

    $job = Job::where('estimate_id', $estimate->id)->firstOrFail();
    expect($job->status)->toBe(Job::STATUS_SCHEDULED);

    // Complete the job
    test()->actingAs($owner)
        ->patch("/owner/jobs/{$job->id}/status", ['status' => Job::STATUS_COMPLETED])
        ->assertRedirect();
    expect($job->fresh()->status)->toBe(Job::STATUS_COMPLETED);

    // Generate invoice
    $job->lineItems()->create(['name' => 'Installation', 'unit_price' => 400, 'quantity' => 1, 'sort_order' => 0]);
    test()->actingAs($owner)->post("/owner/jobs/{$job->id}/invoice")->assertRedirect();
    expect($job->fresh()->invoice)->not->toBeNull();
});

// ── 5. Technician PWA: today's jobs, checklist, notes ────────────────────────

test('technician can fetch today jobs, update notes, and toggle checklist', function () {
    [$owner, $technician, $org, $customer] = e2eSetup();

    $jobType = JobType::factory()->create(['organization_id' => $org->id]);
    $job = Job::factory()->forCustomer($customer)->create([
        'assigned_to'  => $technician->id,
        'status'       => Job::STATUS_SCHEDULED,
        'scheduled_at' => now(),
        'job_type_id'  => $jobType->id,
    ]);
    $checklistItem = $job->checklistItems()->create([
        'label'      => 'Check pressure',
        'is_checked' => false,
        'sort_order' => 0,
    ]);

    // Fetch today's jobs
    test()->actingAs($technician)
        ->getJson('/api/technician/jobs/today')
        ->assertOk()
        ->assertJsonFragment(['id' => $job->id]);

    // Update technician notes
    test()->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/notes", ['technician_notes' => 'Checked all valves'])
        ->assertOk();
    expect($job->fresh()->technician_notes)->toBe('Checked all valves');

    // Toggle checklist item on
    test()->actingAs($technician)
        ->patchJson("/api/technician/jobs/{$job->id}/checklist/{$checklistItem->id}", ['completed' => true])
        ->assertOk();
    expect($checklistItem->fresh()->completed_at)->not->toBeNull();
});

// ── 6. Org isolation across the entire flow ───────────────────────────────────

test('users from different orgs cannot cross-access jobs, invoices, or payments', function () {
    [$owner1] = e2eSetup();
    [$owner2, , $org2, $customer2] = e2eSetup();

    $job = Job::factory()->forCustomer($customer2)->completed()->create();
    $job->lineItems()->create(['name' => 'Work', 'unit_price' => 100, 'quantity' => 1, 'sort_order' => 0]);

    test()->actingAs($owner2)->post("/owner/jobs/{$job->id}/invoice")->assertRedirect();
    $invoice = $job->fresh()->invoice;
    test()->actingAs($owner2)->post("/owner/invoices/{$invoice->id}/send")->assertRedirect();

    // Owner1 cannot see job, invoice, or record payment
    test()->actingAs($owner1)->get("/owner/jobs/{$job->id}")->assertForbidden();
    test()->actingAs($owner1)->get("/owner/invoices/{$invoice->id}")->assertForbidden();
    test()->actingAs($owner1)->post("/owner/invoices/{$invoice->id}/payments", [
        'amount' => 100,
        'method' => 'cash',
    ])->assertForbidden();
});
