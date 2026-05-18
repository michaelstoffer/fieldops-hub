<?php

use App\Models\Customer;
use App\Models\Organization;
use App\Models\Property;
use App\Models\User;

function userWithOrgAndCustomer(): array
{
    $org      = Organization::factory()->create();
    $user     = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    return [$user, $customer];
}

// ── Create / Store ────────────────────────────────────────────────────────────

test('property create form requires authentication', function () {
    $customer = Customer::factory()->create();

    $this->get("/owner/customers/{$customer->id}/properties/create")->assertRedirect('/login');
});

test('user can view the add property form for their customer', function () {
    [$user, $customer] = userWithOrgAndCustomer();

    $this->actingAs($user)
        ->get("/owner/customers/{$customer->id}/properties/create")
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Properties/Create'));
});

test('user cannot add a property to another org\'s customer', function () {
    [$user] = userWithOrgAndCustomer();
    $otherCustomer = Customer::factory()->create();

    $this->actingAs($user)
        ->get("/owner/customers/{$otherCustomer->id}/properties/create")
        ->assertForbidden();
});

test('user can store a property for their customer', function () {
    [$user, $customer] = userWithOrgAndCustomer();

    $this->actingAs($user)
        ->post("/owner/customers/{$customer->id}/properties", [
            'address_line1' => '123 Main St',
            'city'          => 'Springfield',
            'state'         => 'IL',
            'postal_code'   => '62701',
        ])
        ->assertRedirect("/owner/customers/{$customer->id}");

    expect($customer->properties()->count())->toBe(1);
    expect($customer->properties()->first()->organization_id)->toBe($user->organization_id);
});

test('property creation requires address, city, state, and zip', function () {
    [$user, $customer] = userWithOrgAndCustomer();

    $this->actingAs($user)
        ->post("/owner/customers/{$customer->id}/properties", [])
        ->assertSessionHasErrors(['address_line1', 'city', 'state', 'postal_code']);
});

// ── Edit / Update ─────────────────────────────────────────────────────────────

test('user can view the edit property form', function () {
    [$user, $customer] = userWithOrgAndCustomer();
    $property = Property::factory()->forCustomer($customer)->create();

    $this->actingAs($user)
        ->get("/owner/properties/{$property->id}/edit")
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Properties/Edit'));
});

test('user cannot edit a property from another organization', function () {
    [$user] = userWithOrgAndCustomer();
    $property = Property::factory()->create();

    $this->actingAs($user)
        ->get("/owner/properties/{$property->id}/edit")
        ->assertForbidden();
});

test('user can update a property', function () {
    [$user, $customer] = userWithOrgAndCustomer();
    $property = Property::factory()->forCustomer($customer)->create();

    $this->actingAs($user)
        ->patch("/owner/properties/{$property->id}", [
            'address_line1' => '456 New St',
            'city'          => 'Chicago',
            'state'         => 'IL',
            'postal_code'   => '60601',
        ])
        ->assertRedirect("/owner/customers/{$customer->id}");

    expect($property->fresh()->address_line1)->toBe('456 New St');
});

// ── Destroy ───────────────────────────────────────────────────────────────────

test('user can remove their property', function () {
    [$user, $customer] = userWithOrgAndCustomer();
    $property = Property::factory()->forCustomer($customer)->create();

    $this->actingAs($user)
        ->delete("/owner/properties/{$property->id}")
        ->assertRedirect("/owner/customers/{$customer->id}");

    expect(Property::find($property->id))->toBeNull();
    expect(Property::withTrashed()->find($property->id))->not->toBeNull();
});

test('user cannot remove a property from another organization', function () {
    [$user] = userWithOrgAndCustomer();
    $property = Property::factory()->create();

    $this->actingAs($user)
        ->delete("/owner/properties/{$property->id}")
        ->assertForbidden();
});

// ── Customer show includes properties ────────────────────────────────────────

test('customer show page includes properties', function () {
    [$user, $customer] = userWithOrgAndCustomer();
    Property::factory()->forCustomer($customer)->count(2)->create();

    $this->actingAs($user)
        ->get("/owner/customers/{$customer->id}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Owner/Customers/Show')
            ->has('customer.properties', 2)
        );
});

// ── Quick-create (JSON endpoint) ──────────────────────────────────────────────

test('quick-create returns the new property as JSON', function () {
    [$user, $customer] = userWithOrgAndCustomer();

    $response = $this->actingAs($user)
        ->postJson("/owner/customers/{$customer->id}/properties/quick-create", [
            'address_line1' => '123 Main St',
            'city'          => 'Austin',
            'state'         => 'TX',
            'postal_code'   => '78701',
        ]);

    $response->assertCreated()
        ->assertJsonStructure(['id', 'address_line1', 'city', 'state']);

    expect(Property::where('address_line1', '123 Main St')->exists())->toBeTrue();

    $property = Property::where('address_line1', '123 Main St')->first();
    expect($property->customer_id)->toBe($customer->id);
    expect($property->organization_id)->toBe($user->organization_id);
});

test('quick-create property is scoped to the given customer', function () {
    [$user, $customer] = userWithOrgAndCustomer();

    $this->actingAs($user)
        ->postJson("/owner/customers/{$customer->id}/properties/quick-create", [
            'address_line1' => '456 Oak Ave',
            'city'          => 'Dallas',
            'state'         => 'TX',
            'postal_code'   => '75201',
        ]);

    expect(Property::where('address_line1', '456 Oak Ave')->first()->customer_id)
        ->toBe($customer->id);
});

test('quick-create property validates required address fields', function () {
    [$user, $customer] = userWithOrgAndCustomer();

    $this->actingAs($user)
        ->postJson("/owner/customers/{$customer->id}/properties/quick-create", [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['address_line1', 'city', 'state', 'postal_code']);
});

test('quick-create property returns 403 for another org\'s customer', function () {
    [$user] = userWithOrgAndCustomer();
    [, $otherCustomer] = userWithOrgAndCustomer();

    $this->actingAs($user)
        ->postJson("/owner/customers/{$otherCustomer->id}/properties/quick-create", [
            'address_line1' => '789 Elm St',
            'city'          => 'Houston',
            'state'         => 'TX',
            'postal_code'   => '77001',
        ])
        ->assertForbidden();
});

test('quick-create property requires authentication', function () {
    $customer = Customer::factory()->create();

    $this->postJson("/owner/customers/{$customer->id}/properties/quick-create", [])
        ->assertUnauthorized();
});
