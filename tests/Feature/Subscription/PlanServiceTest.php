<?php

use App\Models\Organization;
use App\Models\Subscription;
use App\Models\User;
use App\Services\PlanService;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    (new RolesAndPermissionsSeeder)->run();
});

// ── Constants & labels ────────────────────────────────────────────────────────

test('PlanService exposes the correct plan constants', function () {
    expect(PlanService::PLAN_STARTER)->toBe('starter');
    expect(PlanService::PLAN_GROWTH)->toBe('growth');
    expect(PlanService::PLAN_PRO)->toBe('pro');
});

test('PlanService defines 14 trial days', function () {
    expect(PlanService::TRIAL_DAYS)->toBe(14);
});

test('label returns human readable plan name', function () {
    $service = new PlanService;

    expect($service->label('starter'))->toBe('Starter');
    expect($service->label('growth'))->toBe('Growth');
    expect($service->label('pro'))->toBe('Pro');
});

test('monthlyPrice returns correct prices', function () {
    $service = new PlanService;

    expect($service->monthlyPrice('starter'))->toBe(79);
    expect($service->monthlyPrice('growth'))->toBe(149);
    expect($service->monthlyPrice('pro'))->toBe(249);
});

test('annualPrice returns correct prices', function () {
    $service = new PlanService;

    expect($service->annualPrice('starter'))->toBe(63);
    expect($service->annualPrice('growth'))->toBe(119);
    expect($service->annualPrice('pro'))->toBe(199);
});

test('isValidPlan accepts known plans', function () {
    $service = new PlanService;

    expect($service->isValidPlan('starter'))->toBeTrue();
    expect($service->isValidPlan('growth'))->toBeTrue();
    expect($service->isValidPlan('pro'))->toBeTrue();
});

test('isValidPlan rejects unknown plans', function () {
    $service = new PlanService;

    expect($service->isValidPlan('enterprise'))->toBeFalse();
    expect($service->isValidPlan(''))->toBeFalse();
    expect($service->isValidPlan('GROWTH'))->toBeFalse();
});

// ── Technician limits ─────────────────────────────────────────────────────────

test('technician limit is 3 for starter', function () {
    expect(PlanService::TECHNICIAN_LIMITS['starter'])->toBe(3);
});

test('technician limit is 10 for growth', function () {
    expect(PlanService::TECHNICIAN_LIMITS['growth'])->toBe(10);
});

test('technician limit is null for pro', function () {
    expect(PlanService::TECHNICIAN_LIMITS['pro'])->toBeNull();
});

// ── Trial plan map ─────────────────────────────────────────────────────────────

test('starter org gets growth features during trial', function () {
    expect(PlanService::TRIAL_PLAN_MAP['starter'])->toBe('growth');
});

test('growth org gets growth features during trial', function () {
    expect(PlanService::TRIAL_PLAN_MAP['growth'])->toBe('growth');
});

test('pro org gets pro features during trial', function () {
    expect(PlanService::TRIAL_PLAN_MAP['pro'])->toBe('pro');
});

// ── activePlan ────────────────────────────────────────────────────────────────

test('activePlan returns starter when org has no subscription', function () {
    $org = Organization::factory()->withoutSubscription()->create(['plan' => 'growth']);
    $service = new PlanService;

    expect($service->activePlan($org))->toBe('starter');
});

test('activePlan returns growth features for starter org during trial', function () {
    $org = Organization::factory()->create(['plan' => 'starter', 'trial_ends_at' => now()->addDays(7)]);
    Subscription::factory()->trialing($org)->create();

    $service = new PlanService;
    expect($service->activePlan($org))->toBe('growth');
});

test('activePlan returns growth features for growth org during trial', function () {
    $org = Organization::factory()->create(['plan' => 'growth', 'trial_ends_at' => now()->addDays(7)]);
    Subscription::factory()->trialing($org)->create();

    $service = new PlanService;
    expect($service->activePlan($org))->toBe('growth');
});

test('activePlan returns pro features for pro org during trial', function () {
    $org = Organization::factory()->create(['plan' => 'pro', 'trial_ends_at' => now()->addDays(7)]);
    Subscription::factory()->trialing($org, 'pro')->create();

    $service = new PlanService;
    expect($service->activePlan($org))->toBe('pro');
});

test('activePlan returns starter after trial ends with starter plan', function () {
    $org = Organization::factory()->create(['plan' => 'starter', 'trial_ends_at' => now()->subDay()]);
    Subscription::factory()->active($org, 'starter')->create();

    $service = new PlanService;
    expect($service->activePlan($org))->toBe('starter');
});

test('activePlan returns subscribed plan when active and not trialing', function () {
    $org = Organization::factory()->create(['plan' => 'growth']);
    Subscription::factory()->active($org, 'growth')->create();

    $service = new PlanService;
    expect($service->activePlan($org))->toBe('growth');
});

// ── technicianLimit ───────────────────────────────────────────────────────────

test('technicianLimit returns 10 for growth plan during trial', function () {
    $org = Organization::factory()->create(['plan' => 'starter', 'trial_ends_at' => now()->addDays(7)]);
    Subscription::factory()->trialing($org)->create();

    $service = new PlanService;
    expect($service->technicianLimit($org))->toBe(10); // growth features during trial
});

test('technicianLimit returns null for pro plan', function () {
    $org = Organization::factory()->create(['plan' => 'pro']);
    Subscription::factory()->active($org, 'pro')->create();

    $service = new PlanService;
    expect($service->technicianLimit($org))->toBeNull();
});

// ── atTechnicianLimit ─────────────────────────────────────────────────────────

test('atTechnicianLimit returns false when under the cap', function () {
    $org = Organization::factory()->create(['plan' => 'starter', 'trial_ends_at' => now()->subDay()]);
    Subscription::factory()->active($org, 'starter')->create();

    User::factory()->technician($org)->count(2)->create();

    $service = new PlanService;
    expect($service->atTechnicianLimit($org))->toBeFalse();
});

test('atTechnicianLimit returns true when at the cap', function () {
    $org = Organization::factory()->create(['plan' => 'starter', 'trial_ends_at' => now()->subDay()]);
    Subscription::factory()->active($org, 'starter')->create();

    User::factory()->technician($org)->count(3)->create();

    $service = new PlanService;
    expect($service->atTechnicianLimit($org))->toBeTrue();
});

test('atTechnicianLimit always returns false for pro plan', function () {
    $org = Organization::factory()->create(['plan' => 'pro']);
    Subscription::factory()->active($org, 'pro')->create();

    User::factory()->technician($org)->count(50)->create();

    $service = new PlanService;
    expect($service->atTechnicianLimit($org))->toBeFalse();
});

// ── technicianCount ───────────────────────────────────────────────────────────

test('technicianCount returns only users with technician role', function () {
    $org = Organization::factory()->create();

    User::factory()->technician($org)->count(2)->create();
    User::factory()->create(['organization_id' => $org->id])->assignRole('dispatcher');

    $service = new PlanService;
    expect($service->technicianCount($org))->toBe(2);
});

test('technicianCount returns zero when org has no technicians', function () {
    $org = Organization::factory()->create();

    $service = new PlanService;
    expect($service->technicianCount($org))->toBe(0);
});
