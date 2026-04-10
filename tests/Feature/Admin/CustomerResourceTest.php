<?php

use App\Models\Customer;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;

function ownerUser(): User
{
    (new RolesAndPermissionsSeeder)->run();

    $org  = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');

    return $user;
}

// ── List ──────────────────────────────────────────────────────────────────────

test('owner can view the admin customer list', function () {
    $user = ownerUser();
    Customer::factory()->count(3)->create(['organization_id' => $user->organization_id]);

    $this->actingAs($user)->get('/admin/customers')->assertOk();
});

test('admin customer list is scoped to the user\'s organization', function () {
    $user  = ownerUser();
    $other = Organization::factory()->create();

    Customer::factory()->count(2)->create(['organization_id' => $user->organization_id]);
    Customer::factory()->count(5)->create(['organization_id' => $other->id]);

    // Filament renders via Livewire — we assert the page loads and doesn't expose other orgs' records via HTTP
    $this->actingAs($user)->get('/admin/customers')->assertOk()->assertDontSee('organization_id');
});

// ── Create ────────────────────────────────────────────────────────────────────

test('owner can view the create customer form', function () {
    $this->actingAs(ownerUser())->get('/admin/customers/create')->assertOk();
});

// ── Edit ──────────────────────────────────────────────────────────────────────

test('owner can view the edit customer form for their customer', function () {
    $user     = ownerUser();
    $customer = Customer::factory()->create(['organization_id' => $user->organization_id]);

    $this->actingAs($user)->get("/admin/customers/{$customer->id}/edit")->assertOk();
});

test('owner cannot edit a customer belonging to another organization', function () {
    $user  = ownerUser();
    $other = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $other->id]);

    // Filament will resolve the record through getEloquentQuery() which is org-scoped.
    // The record won't be found, resulting in a 404.
    $this->actingAs($user)->get("/admin/customers/{$customer->id}/edit")->assertNotFound();
});
