<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\JobLineItem;
use App\Models\Organization;
use App\Models\User;

function invoiceSetup(): array
{
    $org      = Organization::factory()->create();
    $user     = User::factory()->create(['organization_id' => $org->id]);
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
