<?php

use App\Models\Customer;
use App\Models\Estimate;
use App\Models\Organization;

test('command marks sent estimates past their expiry date as expired', function () {
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    $expired = Estimate::factory()->forCustomer($customer)->expired()->create();

    $this->artisan('estimates:mark-expired')->assertSuccessful();

    expect($expired->fresh()->status)->toBe(Estimate::STATUS_EXPIRED);
});

test('command does not mark estimates that expire in the future', function () {
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    $future = Estimate::factory()->forCustomer($customer)->sent()->create([
        'expires_at' => now()->addDays(10)->toDateString(),
    ]);

    $this->artisan('estimates:mark-expired')->assertSuccessful();

    expect($future->fresh()->status)->toBe(Estimate::STATUS_SENT);
});

test('command does not affect already accepted or declined estimates', function () {
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    $accepted = Estimate::factory()->forCustomer($customer)->accepted()->create([
        'expires_at' => now()->subDays(5)->toDateString(),
    ]);
    $declined = Estimate::factory()->forCustomer($customer)->create([
        'status'     => Estimate::STATUS_DECLINED,
        'expires_at' => now()->subDays(5)->toDateString(),
    ]);

    $this->artisan('estimates:mark-expired')->assertSuccessful();

    expect($accepted->fresh()->status)->toBe(Estimate::STATUS_ACCEPTED);
    expect($declined->fresh()->status)->toBe(Estimate::STATUS_DECLINED);
});

test('command does not mark estimates with no expiry date', function () {
    $org      = Organization::factory()->create();
    $customer = Customer::factory()->create(['organization_id' => $org->id]);

    $noExpiry = Estimate::factory()->forCustomer($customer)->sent()->create([
        'expires_at' => null,
    ]);

    $this->artisan('estimates:mark-expired')->assertSuccessful();

    expect($noExpiry->fresh()->status)->toBe(Estimate::STATUS_SENT);
});
