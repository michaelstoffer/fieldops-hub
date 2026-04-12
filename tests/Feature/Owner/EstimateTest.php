<?php

use App\Models\Customer;
use App\Models\Estimate;
use App\Models\EstimatePackage;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

function estimateSetup(): array
{
    (new RolesAndPermissionsSeeder)->run();
    $org      = Organization::factory()->create();
    $user     = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    return [$user, $org, $customer];
}

function samplePackages(Customer $customer): array
{
    return [
        [
            'tier'           => 'good',
            'label'          => 'Basic',
            'description'    => null,
            'is_recommended' => false,
            'line_items'     => [
                ['name' => 'Filter replacement', 'unit_price' => 25.00, 'quantity' => 1, 'is_taxable' => true, 'item_id' => null],
            ],
        ],
        [
            'tier'           => 'better',
            'label'          => 'Standard',
            'description'    => 'Includes tune-up',
            'is_recommended' => true,
            'line_items'     => [
                ['name' => 'Filter replacement', 'unit_price' => 25.00, 'quantity' => 1, 'is_taxable' => true, 'item_id' => null],
                ['name' => 'System tune-up',     'unit_price' => 75.00, 'quantity' => 1, 'is_taxable' => true, 'item_id' => null],
            ],
        ],
    ];
}

// ── Index ──────────────────────────────────────────────────────────────────────

test('estimate index requires authentication', function () {
    $this->get('/owner/estimates')->assertRedirect('/login');
});

test('user can view their estimate list', function () {
    [$user, $org, $customer] = estimateSetup();
    Estimate::factory()->forCustomer($customer)->count(3)->create();

    $this->actingAs($user)
        ->get('/owner/estimates')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Owner/Estimates/Index')
            ->has('estimates.data', 3)
        );
});

test('estimate list is scoped to the user\'s organization', function () {
    [$user] = estimateSetup();
    [, , $otherCustomer] = estimateSetup();
    Estimate::factory()->forCustomer($otherCustomer)->count(2)->create();

    $this->actingAs($user)
        ->get('/owner/estimates')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('estimates.data', 0));
});

test('estimate list can be filtered by status', function () {
    [$user, $org, $customer] = estimateSetup();
    Estimate::factory()->forCustomer($customer)->draft()->count(2)->create();
    Estimate::factory()->forCustomer($customer)->sent()->count(3)->create();

    $this->actingAs($user)
        ->get('/owner/estimates?status=sent')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('estimates.data', 3));
});

// ── Create / Store ─────────────────────────────────────────────────────────────

test('user can view the create estimate form', function () {
    [$user] = estimateSetup();

    $this->actingAs($user)
        ->get('/owner/estimates/create')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Estimates/Create'));
});

test('user can create an estimate with packages', function () {
    [$user, $org, $customer] = estimateSetup();

    $this->actingAs($user)
        ->post('/owner/estimates', [
            'customer_id' => $customer->id,
            'job_id'      => null,
            'title'       => 'HVAC Service Estimate',
            'intro'       => null,
            'footer'      => null,
            'expires_at'  => now()->addDays(30)->toDateString(),
            'tax_rate'    => 0,
            'packages'    => samplePackages($customer),
        ])
        ->assertRedirect();

    $estimate = Estimate::where('title', 'HVAC Service Estimate')->first();
    expect($estimate)->not->toBeNull();
    expect($estimate->organization_id)->toBe($org->id);
    expect($estimate->status)->toBe(Estimate::STATUS_DRAFT);
    expect($estimate->packages)->toHaveCount(2);
    expect($estimate->packages->first()->lineItems)->toHaveCount(1);
    expect($estimate->packages->last()->lineItems)->toHaveCount(2);
});

test('creating an estimate generates an estimate number', function () {
    [$user, $org, $customer] = estimateSetup();

    $this->actingAs($user)
        ->post('/owner/estimates', [
            'customer_id' => $customer->id,
            'job_id'      => null,
            'title'       => 'Test',
            'expires_at'  => null,
            'tax_rate'    => 0,
            'packages'    => [
                ['tier' => 'good', 'label' => 'Basic', 'is_recommended' => false, 'line_items' => [
                    ['name' => 'Service', 'unit_price' => 100, 'quantity' => 1, 'is_taxable' => true, 'item_id' => null],
                ]],
            ],
        ])
        ->assertRedirect();

    expect(Estimate::where('title', 'Test')->first()->estimate_number)->toStartWith('EST-');
});

test('package totals are calculated on store', function () {
    [$user, $org, $customer] = estimateSetup();

    $this->actingAs($user)
        ->post('/owner/estimates', [
            'customer_id' => $customer->id,
            'title'       => 'Calc Test',
            'tax_rate'    => 0.10,
            'packages'    => [
                ['tier' => 'good', 'label' => 'Basic', 'is_recommended' => false, 'line_items' => [
                    ['name' => 'Part A', 'unit_price' => 100, 'quantity' => 2, 'is_taxable' => true, 'item_id' => null],
                    ['name' => 'Part B', 'unit_price' => 50,  'quantity' => 1, 'is_taxable' => false, 'item_id' => null],
                ]],
            ],
        ])
        ->assertRedirect();

    $pkg = Estimate::where('title', 'Calc Test')->first()->packages->first();
    expect((float) $pkg->subtotal)->toBe(250.0); // 200 + 50
    expect((float) $pkg->tax_amount)->toBe(20.0); // 10% of taxable 200
    expect((float) $pkg->total)->toBe(270.0);
});

test('estimate creation requires title and customer', function () {
    [$user] = estimateSetup();

    $this->actingAs($user)
        ->post('/owner/estimates', [])
        ->assertSessionHasErrors(['title', 'customer_id']);
});

test('estimate creation requires at least one package', function () {
    [$user, , $customer] = estimateSetup();

    $this->actingAs($user)
        ->post('/owner/estimates', [
            'customer_id' => $customer->id,
            'title'       => 'Test',
            'packages'    => [],
        ])
        ->assertSessionHasErrors(['packages']);
});

test('user cannot create an estimate for another org\'s customer', function () {
    [$user] = estimateSetup();
    [, , $otherCustomer] = estimateSetup();

    $this->actingAs($user)
        ->post('/owner/estimates', [
            'customer_id' => $otherCustomer->id,
            'title'       => 'Hack',
            'packages'    => [
                ['tier' => 'good', 'label' => 'X', 'is_recommended' => false, 'line_items' => [
                    ['name' => 'X', 'unit_price' => 1, 'quantity' => 1, 'is_taxable' => true, 'item_id' => null],
                ]],
            ],
        ])
        ->assertSessionHasErrors(['customer_id']);
});

// ── Show ──────────────────────────────────────────────────────────────────────

test('user can view an estimate that belongs to their org', function () {
    [$user, $org, $customer] = estimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->create();

    $this->actingAs($user)
        ->get("/owner/estimates/{$estimate->id}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Estimates/Show'));
});

test('user cannot view an estimate from another org', function () {
    [$user] = estimateSetup();
    [, , $otherCustomer] = estimateSetup();
    $estimate = Estimate::factory()->forCustomer($otherCustomer)->create();

    $this->actingAs($user)
        ->get("/owner/estimates/{$estimate->id}")
        ->assertForbidden();
});

// ── Edit / Update ─────────────────────────────────────────────────────────────

test('user can edit and update an estimate', function () {
    [$user, $org, $customer] = estimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->create(['title' => 'Old Title']);

    $this->actingAs($user)
        ->patch("/owner/estimates/{$estimate->id}", [
            'customer_id' => $customer->id,
            'title'       => 'Updated Title',
            'tax_rate'    => 0,
            'packages'    => samplePackages($customer),
        ])
        ->assertRedirect("/owner/estimates/{$estimate->id}");

    expect($estimate->fresh()->title)->toBe('Updated Title');
});

test('user cannot update an estimate from another org', function () {
    [$user] = estimateSetup();
    [, , $otherCustomer] = estimateSetup();
    $estimate = Estimate::factory()->forCustomer($otherCustomer)->create();

    $this->actingAs($user)
        ->patch("/owner/estimates/{$estimate->id}", [
            'customer_id' => $otherCustomer->id,
            'title'       => 'Hack',
        ])
        ->assertForbidden();
});

// ── Send ──────────────────────────────────────────────────────────────────────

test('user can send an estimate', function () {
    [$user, $org, $customer] = estimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->draft()->create();

    $this->actingAs($user)
        ->post("/owner/estimates/{$estimate->id}/send")
        ->assertRedirect("/owner/estimates/{$estimate->id}");

    expect($estimate->fresh()->status)->toBe(Estimate::STATUS_SENT);
    expect($estimate->fresh()->sent_at)->not->toBeNull();
});

test('user cannot send another org\'s estimate', function () {
    [$user] = estimateSetup();
    [, , $otherCustomer] = estimateSetup();
    $estimate = Estimate::factory()->forCustomer($otherCustomer)->draft()->create();

    $this->actingAs($user)
        ->post("/owner/estimates/{$estimate->id}/send")
        ->assertForbidden();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

test('user can soft-delete their estimate', function () {
    [$user, $org, $customer] = estimateSetup();
    $estimate = Estimate::factory()->forCustomer($customer)->create();

    $this->actingAs($user)
        ->delete("/owner/estimates/{$estimate->id}")
        ->assertRedirect('/owner/estimates');

    expect(Estimate::find($estimate->id))->toBeNull();
    expect(Estimate::withTrashed()->find($estimate->id))->not->toBeNull();
});

test('user cannot delete another org\'s estimate', function () {
    [$user] = estimateSetup();
    [, , $otherCustomer] = estimateSetup();
    $estimate = Estimate::factory()->forCustomer($otherCustomer)->create();

    $this->actingAs($user)
        ->delete("/owner/estimates/{$estimate->id}")
        ->assertForbidden();
});

// ── Convert to Job ─────────────────────────────────────────────────────────────

test('accepted estimate can be converted to job', function () {
    [$user, $org, $customer] = estimateSetup();

    $estimate = Estimate::factory()->forCustomer($customer)->accepted('better')->create([
        'title'  => 'HVAC Service',
        'footer' => 'Some office notes',
    ]);

    $package = $estimate->packages()->create([
        'tier'           => 'better',
        'label'          => 'Standard',
        'description'    => 'Includes tune-up',
        'is_recommended' => true,
        'subtotal'       => 100,
        'tax_amount'     => 0,
        'total'          => 100,
    ]);

    $package->lineItems()->create([
        'name'        => 'Filter replacement',
        'unit_price'  => 25.00,
        'quantity'    => 1,
        'is_taxable'  => true,
        'sort_order'  => 0,
    ]);

    $package->lineItems()->create([
        'name'        => 'System tune-up',
        'unit_price'  => 75.00,
        'quantity'    => 1,
        'is_taxable'  => true,
        'sort_order'  => 1,
    ]);

    $response = $this->actingAs($user)
        ->post("/owner/estimates/{$estimate->id}/convert");

    $response->assertRedirect();

    $job = \App\Models\Job::where('estimate_id', $estimate->id)->firstOrFail();

    expect($job->title)->toBe('HVAC Service');
    expect($job->description)->toBe('Includes tune-up');
    expect($job->office_notes)->toBe('Some office notes');
    expect($job->customer_id)->toBe($customer->id);
    expect($job->lineItems)->toHaveCount(2);
    expect($job->lineItems->first()->name)->toBe('Filter replacement');
});

test('converting estimate twice returns 422', function () {
    [$user, $org, $customer] = estimateSetup();

    $estimate = Estimate::factory()->forCustomer($customer)->accepted('good')->create();

    $package = $estimate->packages()->create([
        'tier' => 'good', 'label' => 'Basic', 'subtotal' => 0, 'tax_amount' => 0, 'total' => 0,
    ]);

    // First conversion
    $this->actingAs($user)->post("/owner/estimates/{$estimate->id}/convert");

    // Second conversion
    $this->actingAs($user)
        ->post("/owner/estimates/{$estimate->id}/convert")
        ->assertStatus(422);
});

test('converting non-accepted estimate returns 422', function () {
    [$user, $org, $customer] = estimateSetup();

    $estimate = Estimate::factory()->forCustomer($customer)->sent()->create();

    $this->actingAs($user)
        ->post("/owner/estimates/{$estimate->id}/convert")
        ->assertStatus(422);
});

test('user cannot convert another org\'s estimate', function () {
    [$user] = estimateSetup();
    [, , $otherCustomer] = estimateSetup();

    $estimate = Estimate::factory()->forCustomer($otherCustomer)->accepted()->create();

    $this->actingAs($user)
        ->post("/owner/estimates/{$estimate->id}/convert")
        ->assertForbidden();
});

test('convert falls back to first package when accepted_package has no match', function () {
    [$user, $org, $customer] = estimateSetup();

    $estimate = Estimate::factory()->forCustomer($customer)->accepted('best')->create([
        'title' => 'Fallback Test',
    ]);

    // Only create a 'good' package (no 'best' package)
    $package = $estimate->packages()->create([
        'tier' => 'good', 'label' => 'Basic', 'description' => 'Fallback package',
        'subtotal' => 50, 'tax_amount' => 0, 'total' => 50,
    ]);

    $package->lineItems()->create([
        'name' => 'Item A', 'unit_price' => 50, 'quantity' => 1, 'is_taxable' => true, 'sort_order' => 0,
    ]);

    $this->actingAs($user)
        ->post("/owner/estimates/{$estimate->id}/convert")
        ->assertRedirect();

    $job = \App\Models\Job::where('estimate_id', $estimate->id)->firstOrFail();
    expect($job->description)->toBe('Fallback package');
    expect($job->lineItems)->toHaveCount(1);
});
