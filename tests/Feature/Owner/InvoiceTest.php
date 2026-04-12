<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\JobLineItem;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

function invoiceSetup(): array
{
    (new RolesAndPermissionsSeeder)->run();
    $org      = Organization::factory()->create();
    $user     = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    return [$user, $org, $customer];
}

// ── Index ──────────────────────────────────────────────────────────────────────

test('invoice index requires authentication', function () {
    $this->get('/owner/invoices')->assertRedirect('/login');
});

test('user can view their invoice list', function () {
    [$user, $org, $customer] = invoiceSetup();
    Invoice::factory()->forCustomer($customer)->create(['invoice_number' => 'INV-0001']);

    $this->actingAs($user)
        ->get('/owner/invoices')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Owner/Invoices/Index')
            ->has('invoices.data', 1)
        );
});

test('invoice index is scoped to organization', function () {
    [$user] = invoiceSetup();
    [, , $otherCustomer] = invoiceSetup();
    Invoice::factory()->forCustomer($otherCustomer)->create();

    $this->actingAs($user)
        ->get('/owner/invoices')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('invoices.data', 0));
});

test('invoice index can filter by status', function () {
    [$user, $org, $customer] = invoiceSetup();
    Invoice::factory()->forCustomer($customer)->draft()->create();
    Invoice::factory()->forCustomer($customer)->sent()->create();

    $this->actingAs($user)
        ->get('/owner/invoices?status=sent')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('invoices.data', 1));
});

// ── Show ───────────────────────────────────────────────────────────────────────

test('user can view their own invoice', function () {
    [$user, $org, $customer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($customer)->create();

    $this->actingAs($user)
        ->get("/owner/invoices/{$invoice->id}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Invoices/Show'));
});

test('user cannot view another org\'s invoice', function () {
    [$user] = invoiceSetup();
    [, , $otherCustomer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($otherCustomer)->create();

    $this->actingAs($user)
        ->get("/owner/invoices/{$invoice->id}")
        ->assertForbidden();
});

// ── Generate from Job ──────────────────────────────────────────────────────────

test('user can generate invoice from completed job', function () {
    [$user, $org, $customer] = invoiceSetup();
    $job = Job::factory()->forCustomer($customer)->completed()->create(['title' => 'Pump Service']);

    $job->lineItems()->create([
        'name' => 'Labor', 'unit_price' => 150, 'quantity' => 1, 'sort_order' => 0,
    ]);
    $job->lineItems()->create([
        'name' => 'Parts', 'unit_price' => 75, 'quantity' => 2, 'sort_order' => 1,
    ]);

    $response = $this->actingAs($user)
        ->post("/owner/jobs/{$job->id}/invoice");

    $response->assertRedirect();

    $invoice = Invoice::where('job_id', $job->id)->firstOrFail();
    expect($invoice->customer_id)->toBe($customer->id);
    expect($invoice->status)->toBe(Invoice::STATUS_DRAFT);
    expect($invoice->lineItems)->toHaveCount(2);
    expect((float) $invoice->subtotal)->toBe(300.0);
    expect((float) $invoice->total)->toBe(300.0);
});

test('invoice number is auto-incremented', function () {
    [$user, $org, $customer] = invoiceSetup();

    $job1 = Job::factory()->forCustomer($customer)->completed()->create();
    $job2 = Job::factory()->forCustomer($customer)->completed()->create();

    $this->actingAs($user)->post("/owner/jobs/{$job1->id}/invoice");
    $this->actingAs($user)->post("/owner/jobs/{$job2->id}/invoice");

    $numbers = Invoice::where('organization_id', $org->id)
        ->orderBy('id')
        ->pluck('invoice_number')
        ->toArray();

    expect($numbers[0])->toBe('INV-0001');
    expect($numbers[1])->toBe('INV-0002');
});

test('cannot generate invoice from non-completed job', function () {
    [$user, $org, $customer] = invoiceSetup();
    $job = Job::factory()->forCustomer($customer)->scheduled()->create();

    $this->actingAs($user)
        ->post("/owner/jobs/{$job->id}/invoice")
        ->assertStatus(422);
});

test('cannot generate invoice twice for same job', function () {
    [$user, $org, $customer] = invoiceSetup();
    $job = Job::factory()->forCustomer($customer)->completed()->create();

    $this->actingAs($user)->post("/owner/jobs/{$job->id}/invoice");

    $this->actingAs($user)
        ->post("/owner/jobs/{$job->id}/invoice")
        ->assertStatus(422);
});

test('user cannot generate invoice for another org\'s job', function () {
    [$user] = invoiceSetup();
    [, , $otherCustomer] = invoiceSetup();
    $job = Job::factory()->forCustomer($otherCustomer)->completed()->create();

    $this->actingAs($user)
        ->post("/owner/jobs/{$job->id}/invoice")
        ->assertForbidden();
});

// ── Send ───────────────────────────────────────────────────────────────────────

test('user can send a draft invoice', function () {
    [$user, $org, $customer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($customer)->draft()->create();

    $this->actingAs($user)
        ->post("/owner/invoices/{$invoice->id}/send")
        ->assertRedirect();

    expect($invoice->fresh()->status)->toBe(Invoice::STATUS_SENT);
    expect($invoice->fresh()->sent_at)->not->toBeNull();
});

test('cannot send a paid invoice', function () {
    [$user, $org, $customer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($customer)->paid()->create();

    $this->actingAs($user)
        ->post("/owner/invoices/{$invoice->id}/send")
        ->assertStatus(422);
});

test('user cannot send another org\'s invoice', function () {
    [$user] = invoiceSetup();
    [, , $otherCustomer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($otherCustomer)->draft()->create();

    $this->actingAs($user)
        ->post("/owner/invoices/{$invoice->id}/send")
        ->assertForbidden();
});

// ── Void ───────────────────────────────────────────────────────────────────────

test('user can void a sent invoice', function () {
    [$user, $org, $customer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($customer)->sent()->create();

    $this->actingAs($user)
        ->post("/owner/invoices/{$invoice->id}/void")
        ->assertRedirect();

    expect($invoice->fresh()->status)->toBe(Invoice::STATUS_VOID);
});

test('cannot void a paid invoice', function () {
    [$user, $org, $customer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($customer)->paid()->create();

    $this->actingAs($user)
        ->post("/owner/invoices/{$invoice->id}/void")
        ->assertStatus(422);
});

test('cannot void an already-voided invoice', function () {
    [$user, $org, $customer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($customer)->create(['status' => Invoice::STATUS_VOID]);

    $this->actingAs($user)
        ->post("/owner/invoices/{$invoice->id}/void")
        ->assertStatus(422);
});

// ── Destroy ───────────────────────────────────────────────────────────────────

test('user can delete a draft invoice', function () {
    [$user, $org, $customer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($customer)->draft()->create();

    $this->actingAs($user)
        ->delete("/owner/invoices/{$invoice->id}")
        ->assertRedirect('/owner/invoices');

    expect(Invoice::find($invoice->id))->toBeNull();
    expect(Invoice::withTrashed()->find($invoice->id))->not->toBeNull();
});

test('cannot delete a non-draft invoice', function () {
    [$user, $org, $customer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($customer)->sent()->create();

    $this->actingAs($user)
        ->delete("/owner/invoices/{$invoice->id}")
        ->assertStatus(422);
});

test('user cannot delete another org\'s invoice', function () {
    [$user] = invoiceSetup();
    [, , $otherCustomer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($otherCustomer)->draft()->create();

    $this->actingAs($user)
        ->delete("/owner/invoices/{$invoice->id}")
        ->assertForbidden();
});

// ── Record Manual Payment ──────────────────────────────────────────────────────

test('user can record a full cash payment', function () {
    [$user, $org, $customer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($customer)->sent()->create([
        'total'       => 250.00,
        'balance_due' => 250.00,
        'amount_paid' => 0.00,
    ]);

    $this->actingAs($user)
        ->post("/owner/invoices/{$invoice->id}/payments", [
            'amount'  => 250.00,
            'method'  => 'cash',
            'paid_at' => today()->toDateString(),
        ])
        ->assertRedirect();

    $invoice->refresh();
    expect($invoice->status)->toBe(\App\Models\Invoice::STATUS_PAID);
    expect((float) $invoice->balance_due)->toBe(0.0);
    expect((float) $invoice->amount_paid)->toBe(250.0);
    expect($invoice->paid_at)->not->toBeNull();

    $payment = \App\Models\Payment::where('invoice_id', $invoice->id)->first();
    expect($payment->method)->toBe('cash');
    expect((float) $payment->amount)->toBe(250.0);
    expect($payment->recorded_by)->toBe($user->id);
});

test('user can record a partial check payment', function () {
    [$user, $org, $customer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($customer)->sent()->create([
        'total'       => 500.00,
        'balance_due' => 500.00,
        'amount_paid' => 0.00,
    ]);

    $this->actingAs($user)
        ->post("/owner/invoices/{$invoice->id}/payments", [
            'amount'    => 200.00,
            'method'    => 'check',
            'reference' => '1042',
            'paid_at'   => today()->toDateString(),
        ])
        ->assertRedirect();

    $invoice->refresh();
    expect($invoice->status)->toBe(\App\Models\Invoice::STATUS_PARTIAL);
    expect((float) $invoice->balance_due)->toBe(300.0);
    expect((float) $invoice->amount_paid)->toBe(200.0);
});

test('payment amount cannot exceed balance due', function () {
    [$user, $org, $customer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($customer)->sent()->create([
        'total'       => 100.00,
        'balance_due' => 100.00,
    ]);

    $this->actingAs($user)
        ->post("/owner/invoices/{$invoice->id}/payments", [
            'amount'  => 999.00,
            'method'  => 'cash',
            'paid_at' => today()->toDateString(),
        ])
        ->assertSessionHasErrors('amount');
});

test('payment requires a valid method', function () {
    [$user, $org, $customer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($customer)->sent()->create([
        'total'       => 100.00,
        'balance_due' => 100.00,
    ]);

    $this->actingAs($user)
        ->post("/owner/invoices/{$invoice->id}/payments", [
            'amount'  => 50.00,
            'method'  => 'stripe', // not allowed for manual recording
            'paid_at' => today()->toDateString(),
        ])
        ->assertSessionHasErrors('method');
});

test('cannot record payment on a paid invoice', function () {
    [$user, $org, $customer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($customer)->paid()->create();

    $this->actingAs($user)
        ->post("/owner/invoices/{$invoice->id}/payments", [
            'amount'  => 10.00,
            'method'  => 'cash',
            'paid_at' => today()->toDateString(),
        ])
        ->assertStatus(422);
});

test('cannot record payment on a voided invoice', function () {
    [$user, $org, $customer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($customer)->create([
        'status'      => \App\Models\Invoice::STATUS_VOID,
        'balance_due' => 100.00,
    ]);

    $this->actingAs($user)
        ->post("/owner/invoices/{$invoice->id}/payments", [
            'amount'  => 10.00,
            'method'  => 'cash',
            'paid_at' => today()->toDateString(),
        ])
        ->assertStatus(422);
});

test('user cannot record payment for another org\'s invoice', function () {
    [$user] = invoiceSetup();
    [, , $otherCustomer] = invoiceSetup();
    $invoice = Invoice::factory()->forCustomer($otherCustomer)->sent()->create([
        'total'       => 100.00,
        'balance_due' => 100.00,
    ]);

    $this->actingAs($user)
        ->post("/owner/invoices/{$invoice->id}/payments", [
            'amount'  => 100.00,
            'method'  => 'cash',
            'paid_at' => today()->toDateString(),
        ])
        ->assertForbidden();
});
