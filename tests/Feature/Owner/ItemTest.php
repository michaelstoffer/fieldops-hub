<?php

use App\Models\Item;
use App\Models\Organization;
use App\Models\User;

function itemUser(): array
{
    $org  = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('owner');

    return [$user, $org];
}

// ── Index ──────────────────────────────────────────────────────────────────────

test('catalog item index requires authentication', function () {
    $this->get('/owner/items')->assertRedirect('/login');
});

test('user can view their catalog item list', function () {
    [$user, $org] = itemUser();
    Item::factory()->count(3)->create(['organization_id' => $org->id]);

    $this->actingAs($user)
        ->get('/owner/items')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Owner/Items/Index')
            ->has('items', 3)
        );
});

test('catalog item list is scoped to organization', function () {
    [$user] = itemUser();
    [, $otherOrg] = itemUser();

    Item::factory()->count(4)->create(['organization_id' => $otherOrg->id]);

    $this->actingAs($user)
        ->get('/owner/items')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('items', 0));
});

// ── Create / Store ─────────────────────────────────────────────────────────────

test('user can view the create catalog item form', function () {
    [$user] = itemUser();

    $this->actingAs($user)
        ->get('/owner/items/create')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Owner/Items/Create'));
});

test('user can create a catalog item', function () {
    [$user, $org] = itemUser();

    $this->actingAs($user)
        ->post('/owner/items', [
            'name'        => 'HVAC Filter',
            'sku'         => 'SKU-001',
            'description' => 'Standard 16x20 filter',
            'unit_price'  => 25.00,
            'unit'        => 'each',
            'is_taxable'  => true,
            'is_active'   => true,
        ])
        ->assertRedirect('/owner/items');

    $item = Item::where('name', 'HVAC Filter')->first();
    expect($item)->not->toBeNull();
    expect($item->organization_id)->toBe($org->id);
    expect($item->sku)->toBe('SKU-001');
    expect((float) $item->unit_price)->toBe(25.00);
    expect($item->is_active)->toBeTrue();
});

test('catalog item creation is scoped to authenticated user\'s organization', function () {
    [$user] = itemUser();

    $this->actingAs($user)
        ->post('/owner/items', [
            'name'       => 'Labor Hour',
            'unit_price' => 95.00,
            'unit'       => 'hr',
            'is_taxable' => false,
            'is_active'  => true,
        ]);

    expect(Item::where('name', 'Labor Hour')->first()->organization_id)
        ->toBe($user->organization_id);
});

test('catalog item creation requires name and unit price', function () {
    [$user] = itemUser();

    $this->actingAs($user)
        ->post('/owner/items', [])
        ->assertSessionHasErrors(['name', 'unit_price', 'unit']);
});

test('catalog item name must not exceed 255 characters', function () {
    [$user] = itemUser();

    $this->actingAs($user)
        ->post('/owner/items', [
            'name'       => str_repeat('x', 256),
            'unit_price' => 10,
            'unit'       => 'each',
        ])
        ->assertSessionHasErrors('name');
});

test('catalog item unit price cannot be negative', function () {
    [$user] = itemUser();

    $this->actingAs($user)
        ->post('/owner/items', [
            'name'       => 'Bad Item',
            'unit_price' => -5,
            'unit'       => 'each',
        ])
        ->assertSessionHasErrors('unit_price');
});

// ── Edit / Update ──────────────────────────────────────────────────────────────

test('user can view the edit form for their catalog item', function () {
    [$user, $org] = itemUser();
    $item = Item::factory()->create(['organization_id' => $org->id]);

    $this->actingAs($user)
        ->get("/owner/items/{$item->id}/edit")
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Owner/Items/Edit')
            ->where('item.id', $item->id)
        );
});

test('user cannot view edit form for another org\'s catalog item', function () {
    [$user] = itemUser();
    [, $otherOrg] = itemUser();
    $item = Item::factory()->create(['organization_id' => $otherOrg->id]);

    $this->actingAs($user)
        ->get("/owner/items/{$item->id}/edit")
        ->assertForbidden();
});

test('user can update their catalog item', function () {
    [$user, $org] = itemUser();
    $item = Item::factory()->create([
        'organization_id' => $org->id,
        'name'            => 'Old Name',
        'unit_price'      => 10.00,
    ]);

    $this->actingAs($user)
        ->patch("/owner/items/{$item->id}", [
            'name'       => 'New Name',
            'unit_price' => 49.99,
            'unit'       => 'hr',
            'is_taxable' => false,
            'is_active'  => true,
        ])
        ->assertRedirect('/owner/items');

    expect($item->fresh()->name)->toBe('New Name');
    expect((float) $item->fresh()->unit_price)->toBe(49.99);
    expect($item->fresh()->is_taxable)->toBeFalse();
});

test('user cannot update another org\'s catalog item', function () {
    [$user] = itemUser();
    [, $otherOrg] = itemUser();
    $item = Item::factory()->create(['organization_id' => $otherOrg->id]);

    $this->actingAs($user)
        ->patch("/owner/items/{$item->id}", [
            'name'       => 'Hijacked',
            'unit_price' => 0,
            'unit'       => 'each',
        ])
        ->assertForbidden();
});

test('catalog item update requires name and unit price', function () {
    [$user, $org] = itemUser();
    $item = Item::factory()->create(['organization_id' => $org->id]);

    $this->actingAs($user)
        ->patch("/owner/items/{$item->id}", [])
        ->assertSessionHasErrors(['name', 'unit_price', 'unit']);
});

// ── Deactivate (Destroy) ───────────────────────────────────────────────────────

test('user can deactivate their catalog item', function () {
    [$user, $org] = itemUser();
    $item = Item::factory()->create(['organization_id' => $org->id, 'is_active' => true]);

    $this->actingAs($user)
        ->delete("/owner/items/{$item->id}")
        ->assertRedirect('/owner/items');

    expect($item->fresh()->is_active)->toBeFalse();
    expect(Item::find($item->id))->not->toBeNull();
});

test('user cannot deactivate another org\'s catalog item', function () {
    [$user] = itemUser();
    [, $otherOrg] = itemUser();
    $item = Item::factory()->create(['organization_id' => $otherOrg->id, 'is_active' => true]);

    $this->actingAs($user)
        ->delete("/owner/items/{$item->id}")
        ->assertForbidden();

    expect($item->fresh()->is_active)->toBeTrue();
});
