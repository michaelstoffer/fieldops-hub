<?php

use App\Models\Customer;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

// Helper: create an owner user with an organization
function userWithOrg(): User
{
    (new RolesAndPermissionsSeeder)->run();
    $org  = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');

    return $user;
}

// ── Index ────────────────────────────────────────────────────────────────────

test('customer index requires authentication', function () {
    $this->get('/owner/customers')->assertRedirect('/login');
});

test('authenticated user can view their customer list', function () {
    $user = userWithOrg();
    Customer::factory()->count(3)->create(['organization_id' => $user->organization_id]);

    $this->actingAs($user)
        ->get('/owner/customers')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Owner/Customers/Index')
            ->has('customers.data', 3)
        );
});

test('customer list is scoped to the authenticated user\'s organization', function () {
    $user = userWithOrg();
    $other = userWithOrg();

    Customer::factory()->count(2)->create(['organization_id' => $user->organization_id]);
    Customer::factory()->count(5)->create(['organization_id' => $other->organization_id]);

    $this->actingAs($user)
        ->get('/owner/customers')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('customers.data', 2));
});

test('customer list can be searched by name', function () {
    $user = userWithOrg();
    Customer::factory()->create(['organization_id' => $user->organization_id, 'first_name' => 'Alice', 'last_name' => 'Anderson']);
    Customer::factory()->create(['organization_id' => $user->organization_id, 'first_name' => 'Bob', 'last_name' => 'Builder']);

    $this->actingAs($user)
        ->get('/owner/customers?search=Alice')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('customers.data', 1));
});

// ── Show ─────────────────────────────────────────────────────────────────────

test('user can view a customer that belongs to their organization', function () {
    $user = userWithOrg();
    $customer = Customer::factory()->create(['organization_id' => $user->organization_id]);

    $this->actingAs($user)
        ->get("/owner/customers/{$customer->id}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Customers/Show'));
});

test('user cannot view a customer from another organization', function () {
    $user = userWithOrg();
    $other = userWithOrg();
    $customer = Customer::factory()->create(['organization_id' => $other->organization_id]);

    $this->actingAs($user)
        ->get("/owner/customers/{$customer->id}")
        ->assertForbidden();
});

// ── Create / Store ────────────────────────────────────────────────────────────

test('user can view the create customer form', function () {
    $user = userWithOrg();

    $this->actingAs($user)
        ->get('/owner/customers/create')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Customers/Create'));
});

test('user can create a customer', function () {
    $user = userWithOrg();

    $this->actingAs($user)
        ->post('/owner/customers', [
            'first_name' => 'Jane',
            'last_name'  => 'Doe',
            'email'      => 'jane@example.com',
            'phone'      => '555-0100',
            'mobile'     => null,
            'notes'      => null,
        ])
        ->assertRedirect();

    expect(Customer::where('email', 'jane@example.com')->exists())->toBeTrue();

    $customer = Customer::where('email', 'jane@example.com')->first();
    expect($customer->organization_id)->toBe($user->organization_id);
});

test('customer creation requires first and last name', function () {
    $user = userWithOrg();

    $this->actingAs($user)
        ->post('/owner/customers', ['first_name' => '', 'last_name' => ''])
        ->assertSessionHasErrors(['first_name', 'last_name']);
});

// ── Edit / Update ─────────────────────────────────────────────────────────────

test('user can view the edit form for their customer', function () {
    $user = userWithOrg();
    $customer = Customer::factory()->create(['organization_id' => $user->organization_id]);

    $this->actingAs($user)
        ->get("/owner/customers/{$customer->id}/edit")
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Customers/Edit'));
});

test('user cannot edit a customer from another organization', function () {
    $user = userWithOrg();
    $other = userWithOrg();
    $customer = Customer::factory()->create(['organization_id' => $other->organization_id]);

    $this->actingAs($user)
        ->get("/owner/customers/{$customer->id}/edit")
        ->assertForbidden();
});

test('user can update their customer', function () {
    $user = userWithOrg();
    $customer = Customer::factory()->create(['organization_id' => $user->organization_id]);

    $this->actingAs($user)
        ->patch("/owner/customers/{$customer->id}", [
            'first_name' => 'Updated',
            'last_name'  => 'Name',
            'email'      => 'updated@example.com',
            'phone'      => null,
            'mobile'     => null,
            'notes'      => null,
        ])
        ->assertRedirect("/owner/customers/{$customer->id}");

    expect($customer->fresh()->first_name)->toBe('Updated');
});

test('user cannot update a customer from another organization', function () {
    $user = userWithOrg();
    $other = userWithOrg();
    $customer = Customer::factory()->create(['organization_id' => $other->organization_id]);

    $this->actingAs($user)
        ->patch("/owner/customers/{$customer->id}", [
            'first_name' => 'Hack',
            'last_name'  => 'Attempt',
        ])
        ->assertForbidden();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

test('user can archive (soft-delete) their customer', function () {
    $user = userWithOrg();
    $customer = Customer::factory()->create(['organization_id' => $user->organization_id]);

    $this->actingAs($user)
        ->delete("/owner/customers/{$customer->id}")
        ->assertRedirect('/owner/customers');

    // find() applies the SoftDeletes scope, so the record should not be found
    expect(Customer::find($customer->id))->toBeNull();
    // withTrashed() bypasses the scope, confirming the record still exists
    expect(Customer::withTrashed()->find($customer->id))->not->toBeNull();
});

test('user cannot archive a customer from another organization', function () {
    $user = userWithOrg();
    $other = userWithOrg();
    $customer = Customer::factory()->create(['organization_id' => $other->organization_id]);

    $this->actingAs($user)
        ->delete("/owner/customers/{$customer->id}")
        ->assertForbidden();

    expect($customer->fresh())->not->toBeNull();
});
