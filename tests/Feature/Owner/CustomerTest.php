<?php

use App\Models\Customer;
use App\Models\Organization;
use App\Models\User;

// Helper: create an owner user with an organization
function userWithOrg(): User
{
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

// ── Quick-create (JSON endpoint) ──────────────────────────────────────────────

test('quick-create returns the new customer as JSON', function () {
    $user = userWithOrg();

    $response = $this->actingAs($user)
        ->postJson('/owner/customers/quick-create', [
            'first_name' => 'Quick',
            'last_name'  => 'Jones',
            'email'      => 'quick@example.com',
            'phone'      => null,
            'mobile'     => null,
            'notes'      => null,
        ]);

    $response->assertCreated()
        ->assertJsonStructure(['id', 'first_name', 'last_name', 'properties']);

    expect(Customer::where('email', 'quick@example.com')->exists())->toBeTrue();
    expect(Customer::where('email', 'quick@example.com')->first()->organization_id)
        ->toBe($user->organization_id);
});

test('quick-create scopes new customer to authenticated user\'s organization', function () {
    $user = userWithOrg();

    $this->actingAs($user)
        ->postJson('/owner/customers/quick-create', [
            'first_name' => 'Scoped',
            'last_name'  => 'Customer',
        ]);

    expect(Customer::where('last_name', 'Customer')->first()->organization_id)
        ->toBe($user->organization_id);
});

test('quick-create validates required fields', function () {
    $user = userWithOrg();

    $this->actingAs($user)
        ->postJson('/owner/customers/quick-create', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['first_name', 'last_name']);
});

test('quick-create requires authentication', function () {
    $this->postJson('/owner/customers/quick-create', [
        'first_name' => 'Ghost',
        'last_name'  => 'User',
    ])->assertUnauthorized();
});

// ── Import page ───────────────────────────────────────────────────────────────

test('user can view the import customers page', function () {
    $user = userWithOrg();

    $this->actingAs($user)
        ->get('/owner/customers/import')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Customers/Import'));
});

test('import page requires authentication', function () {
    $this->get('/owner/customers/import')->assertRedirect('/login');
});

// ── Import (CSV upload) ───────────────────────────────────────────────────────

test('user can import customers from a CSV file', function () {
    $user = userWithOrg();

    $csv = implode("\n", [
        'first_name,last_name,email,phone,mobile,notes',
        'Alice,Smith,alice@example.com,555-0101,,',
        'Bob,Jones,bob@example.com,,,',
    ]);

    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('customers.csv', $csv);

    $this->actingAs($user)
        ->post('/owner/customers/import', ['file' => $file])
        ->assertRedirect('/owner/customers')
        ->assertSessionHas('success');

    expect(Customer::where('organization_id', $user->organization_id)->count())->toBe(2);
    expect(Customer::where('email', 'alice@example.com')->exists())->toBeTrue();
    expect(Customer::where('email', 'bob@example.com')->exists())->toBeTrue();
});

test('import assigns customers to the authenticated user\'s organization', function () {
    $user = userWithOrg();

    $csv = "first_name,last_name\nCarol,White\n";
    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('customers.csv', $csv);

    $this->actingAs($user)
        ->post('/owner/customers/import', ['file' => $file]);

    expect(Customer::where('last_name', 'White')->first()->organization_id)
        ->toBe($user->organization_id);
});

test('import skips rows missing first_name or last_name', function () {
    $user = userWithOrg();

    $csv = implode("\n", [
        'first_name,last_name,email',
        'Valid,Person,valid@example.com',
        ',,empty@example.com',          // missing both — skipped
        ',NoFirst,nofirst@example.com', // missing first_name — skipped
        'NoLast,,nolast@example.com',   // missing last_name — skipped
    ]);

    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('customers.csv', $csv);

    $this->actingAs($user)
        ->post('/owner/customers/import', ['file' => $file])
        ->assertSessionHas('success');

    expect(Customer::where('organization_id', $user->organization_id)->count())->toBe(1);
    expect(Customer::where('email', 'valid@example.com')->exists())->toBeTrue();
    expect(Customer::where('email', 'empty@example.com')->exists())->toBeFalse();
    expect(Customer::where('email', 'nofirst@example.com')->exists())->toBeFalse();
    expect(Customer::where('email', 'nolast@example.com')->exists())->toBeFalse();
});

test('import requires a file', function () {
    $user = userWithOrg();

    $this->actingAs($user)
        ->post('/owner/customers/import', [])
        ->assertSessionHasErrors('file');
});

test('import rejects non-csv files', function () {
    $user = userWithOrg();

    $file = \Illuminate\Http\UploadedFile::fake()->create('customers.pdf', 50, 'application/pdf');

    $this->actingAs($user)
        ->post('/owner/customers/import', ['file' => $file])
        ->assertSessionHasErrors('file');
});

test('import requires authentication', function () {
    $this->post('/owner/customers/import', [])->assertRedirect('/login');
});

test('import skips rows with invalid email format', function () {
    $user = userWithOrg();

    $csv = implode("\n", [
        'first_name,last_name,email',
        'Valid,Person,valid@example.com',
        'Bad,Email,not-an-email',
        'Also,Bad,missing@',
    ]);

    $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('customers.csv', $csv);

    $this->actingAs($user)
        ->post('/owner/customers/import', ['file' => $file])
        ->assertSessionHas('success');

    expect(Customer::where('organization_id', $user->organization_id)->count())->toBe(1);
    expect(Customer::where('email', 'valid@example.com')->exists())->toBeTrue();
    expect(Customer::where('email', 'not-an-email')->exists())->toBeFalse();
    expect(Customer::where('email', 'missing@')->exists())->toBeFalse();
});
