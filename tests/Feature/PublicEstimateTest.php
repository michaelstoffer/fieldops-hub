<?php

use App\Models\Customer;
use App\Models\Estimate;
use App\Models\EstimatePackage;
use App\Models\Organization;
use App\Models\User;

function sentEstimateWithPackages(): Estimate
{
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    $estimate = Estimate::factory()->forCustomer($customer)->sent()->create();

    $estimate->packages()->create([
        'tier' => 'good', 'label' => 'Basic', 'is_recommended' => false,
        'subtotal' => 100, 'tax_amount' => 0, 'total' => 100,
    ]);
    $estimate->packages()->create([
        'tier' => 'better', 'label' => 'Standard', 'is_recommended' => true,
        'subtotal' => 200, 'tax_amount' => 0, 'total' => 200,
    ]);

    return $estimate->fresh(['packages']);
}

// ── Public view ───────────────────────────────────────────────────────────────

test('customer can view a sent estimate by token', function () {
    $estimate = sentEstimateWithPackages();

    $this->get("/estimates/{$estimate->token}")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Public/Estimate')
            ->where('estimate.id', $estimate->id)
        );
});

test('draft estimate returns 404 on public page', function () {
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);
    $estimate = Estimate::factory()->forCustomer($customer)->draft()->create();

    $this->get("/estimates/{$estimate->token}")->assertNotFound();
});

test('invalid token returns 404', function () {
    $this->get('/estimates/totally-invalid-token')->assertNotFound();
});

// ── Accept ────────────────────────────────────────────────────────────────────

test('customer can accept a sent estimate', function () {
    $estimate = sentEstimateWithPackages();

    $this->post("/estimates/{$estimate->token}/accept", ['tier' => 'good'])
        ->assertRedirect("/estimates/{$estimate->token}");

    expect($estimate->fresh()->status)->toBe(Estimate::STATUS_ACCEPTED);
    expect($estimate->fresh()->accepted_package)->toBe('good');
    expect($estimate->fresh()->accepted_at)->not->toBeNull();
});

test('cannot accept with a tier that has no package', function () {
    $estimate = sentEstimateWithPackages(); // has good & better

    $this->post("/estimates/{$estimate->token}/accept", ['tier' => 'best'])
        ->assertStatus(422);
});

test('cannot accept an already accepted estimate', function () {
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);
    $estimate = Estimate::factory()->forCustomer($customer)->accepted('good')->create();

    $this->post("/estimates/{$estimate->token}/accept", ['tier' => 'good'])
        ->assertStatus(422);
});

test('accept requires a tier', function () {
    $estimate = sentEstimateWithPackages();

    $this->post("/estimates/{$estimate->token}/accept", [])
        ->assertSessionHasErrors(['tier']);
});

// ── Decline ───────────────────────────────────────────────────────────────────

test('customer can decline a sent estimate', function () {
    $estimate = sentEstimateWithPackages();

    $this->post("/estimates/{$estimate->token}/decline")
        ->assertRedirect("/estimates/{$estimate->token}");

    expect($estimate->fresh()->status)->toBe(Estimate::STATUS_DECLINED);
    expect($estimate->fresh()->declined_at)->not->toBeNull();
});

test('cannot decline an already accepted estimate', function () {
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);
    $estimate = Estimate::factory()->forCustomer($customer)->accepted('good')->create();

    $this->post("/estimates/{$estimate->token}/decline")->assertStatus(422);
});
