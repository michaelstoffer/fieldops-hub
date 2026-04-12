<?php

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

function billingSetup(): array
{
    (new RolesAndPermissionsSeeder)->run();
    $org      = Organization::factory()->create();
    $user     = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    return [$user, $org, $customer];
}

test('billing dashboard requires authentication', function () {
    $this->get('/owner/billing')->assertRedirect('/login');
});

test('user can view billing dashboard', function () {
    [$user] = billingSetup();

    $this->actingAs($user)
        ->get('/owner/billing')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Billing/Dashboard'));
});

test('billing dashboard shows stats for own org only', function () {
    [$user, $org, $customer] = billingSetup();
    [, , $otherCustomer]     = billingSetup();

    Invoice::factory()->forCustomer($customer)->sent()->create(['total' => 100, 'balance_due' => 100]);
    Invoice::factory()->forCustomer($otherCustomer)->sent()->create(['total' => 999, 'balance_due' => 999]);

    $this->actingAs($user)
        ->get('/owner/billing')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Owner/Billing/Dashboard')
            ->where('stats.open_count', 1)
            ->where('invoices.total', 1)
        );
});

test('billing dashboard can filter invoices by status', function () {
    [$user, $org, $customer] = billingSetup();
    Invoice::factory()->forCustomer($customer)->draft()->create();
    Invoice::factory()->forCustomer($customer)->sent()->create();
    Invoice::factory()->forCustomer($customer)->paid()->create();

    $this->actingAs($user)
        ->get('/owner/billing?status=sent')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('invoices.data', 1));
});

test('billing dashboard can search invoices by number', function () {
    [$user, $org, $customer] = billingSetup();
    Invoice::factory()->forCustomer($customer)->create(['invoice_number' => 'INV-0001']);
    Invoice::factory()->forCustomer($customer)->create(['invoice_number' => 'INV-0002']);

    $this->actingAs($user)
        ->get('/owner/billing?search=INV-0001')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('invoices.data', 1));
});

test('billing stats outstanding_balance sums sent partial and overdue invoices', function () {
    [$user, $org, $customer] = billingSetup();
    Invoice::factory()->forCustomer($customer)->sent()->create(['balance_due' => 200]);
    Invoice::factory()->forCustomer($customer)->create(['status' => 'partial', 'balance_due' => 100, 'total' => 300, 'amount_paid' => 200]);
    Invoice::factory()->forCustomer($customer)->paid()->create(['balance_due' => 0]);

    $this->actingAs($user)
        ->get('/owner/billing')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('stats.open_count', 2)
        );
});
