<?php

use App\Models\Subscription;

// ── isTrialing ────────────────────────────────────────────────────────────────

test('isTrialing returns true when status is trialing and trial has not ended', function () {
    $sub = new Subscription([
        'status'        => Subscription::STATUS_TRIALING,
        'trial_ends_at' => now()->addDays(5),
    ]);

    expect($sub->isTrialing())->toBeTrue();
});

test('isTrialing returns false when trial_ends_at is in the past', function () {
    $sub = new Subscription([
        'status'        => Subscription::STATUS_TRIALING,
        'trial_ends_at' => now()->subDay(),
    ]);

    expect($sub->isTrialing())->toBeFalse();
});

test('isTrialing returns false when status is active', function () {
    $sub = new Subscription([
        'status'        => Subscription::STATUS_ACTIVE,
        'trial_ends_at' => now()->addDays(5),
    ]);

    expect($sub->isTrialing())->toBeFalse();
});

test('isTrialing returns false when trial_ends_at is null', function () {
    $sub = new Subscription([
        'status'        => Subscription::STATUS_TRIALING,
        'trial_ends_at' => null,
    ]);

    expect($sub->isTrialing())->toBeFalse();
});

// ── isActive ──────────────────────────────────────────────────────────────────

test('isActive returns true for active status', function () {
    $sub = new Subscription([
        'status'        => Subscription::STATUS_ACTIVE,
        'trial_ends_at' => null,
    ]);

    expect($sub->isActive())->toBeTrue();
});

test('isActive returns true for trialing with future trial end', function () {
    $sub = new Subscription([
        'status'        => Subscription::STATUS_TRIALING,
        'trial_ends_at' => now()->addDays(3),
    ]);

    expect($sub->isActive())->toBeTrue();
});

test('isActive returns false for trialing with expired trial', function () {
    $sub = new Subscription([
        'status'        => Subscription::STATUS_TRIALING,
        'trial_ends_at' => now()->subHour(),
    ]);

    expect($sub->isActive())->toBeFalse();
});

test('isActive returns false for canceled status', function () {
    $sub = new Subscription([
        'status'        => Subscription::STATUS_CANCELED,
        'trial_ends_at' => null,
    ]);

    expect($sub->isActive())->toBeFalse();
});

test('isActive returns false for past_due status', function () {
    $sub = new Subscription([
        'status'        => Subscription::STATUS_PAST_DUE,
        'trial_ends_at' => null,
    ]);

    // past_due is not in the allowed statuses for isActive
    expect($sub->isActive())->toBeFalse();
});

// ── trialDaysRemaining ────────────────────────────────────────────────────────

test('trialDaysRemaining returns correct days when trial is active', function () {
    $sub = new Subscription([
        'trial_ends_at' => now()->addDays(3)->endOfDay(),
    ]);

    expect($sub->trialDaysRemaining())->toBe(3);
});

test('trialDaysRemaining returns 0 when trial has expired', function () {
    $sub = new Subscription([
        'trial_ends_at' => now()->subDays(2),
    ]);

    expect($sub->trialDaysRemaining())->toBe(0);
});

test('trialDaysRemaining returns 0 when trial_ends_at is null', function () {
    $sub = new Subscription([
        'trial_ends_at' => null,
    ]);

    expect($sub->trialDaysRemaining())->toBe(0);
});

// ── Status constants ──────────────────────────────────────────────────────────

test('Subscription defines expected status constants', function () {
    expect(Subscription::STATUS_TRIALING)->toBe('trialing');
    expect(Subscription::STATUS_ACTIVE)->toBe('active');
    expect(Subscription::STATUS_PAST_DUE)->toBe('past_due');
    expect(Subscription::STATUS_CANCELED)->toBe('canceled');
    expect(Subscription::STATUS_PAUSED)->toBe('paused');
});
