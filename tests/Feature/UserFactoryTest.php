<?php

use App\Models\User;

test('password is automatically hashed when saved', function () {
    $user = User::factory()->create(['password' => 'plain-text-secret']);

    expect($user->password)->not->toBe('plain-text-secret');
    expect(\Illuminate\Support\Facades\Hash::check('plain-text-secret', $user->password))->toBeTrue();
});

test('email_verified_at is cast to a Carbon instance', function () {
    $user = User::factory()->create();

    expect($user->email_verified_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

test('two_factor_confirmed_at is cast to a Carbon instance when set', function () {
    $user = User::factory()->withTwoFactor()->create();

    expect($user->two_factor_confirmed_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
});

test('factory unverified state sets email_verified_at to null', function () {
    $user = User::factory()->unverified()->create();

    expect($user->email_verified_at)->toBeNull();
});

test('factory withoutTwoFactor state clears all 2fa fields', function () {
    $user = User::factory()->withoutTwoFactor()->create();

    expect($user->two_factor_secret)->toBeNull();
    expect($user->two_factor_recovery_codes)->toBeNull();
    expect($user->two_factor_confirmed_at)->toBeNull();
});

test('factory default state has no 2fa configured', function () {
    $user = User::factory()->create();

    expect($user->two_factor_secret)->toBeNull();
    expect($user->two_factor_recovery_codes)->toBeNull();
    expect($user->two_factor_confirmed_at)->toBeNull();
});

test('hasEnabledTwoFactorAuthentication returns false when 2fa not configured', function () {
    $user = User::factory()->withoutTwoFactor()->create();

    expect($user->hasEnabledTwoFactorAuthentication())->toBeFalse();
});

test('hasEnabledTwoFactorAuthentication returns true when 2fa is confirmed', function () {
    $user = User::factory()->withTwoFactor()->create();

    expect($user->hasEnabledTwoFactorAuthentication())->toBeTrue();
});

test('password is not exposed in toArray output', function () {
    $user = User::factory()->make();

    expect(array_key_exists('password', $user->toArray()))->toBeFalse();
});

test('two_factor_secret is not exposed in toArray output', function () {
    $user = User::factory()->withTwoFactor()->make();

    expect(array_key_exists('two_factor_secret', $user->toArray()))->toBeFalse();
    expect(array_key_exists('two_factor_recovery_codes', $user->toArray()))->toBeFalse();
});
