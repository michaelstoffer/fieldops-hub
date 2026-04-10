<?php

use App\Models\User;
use Laravel\Fortify\TwoFactorAuthenticatable;

test('user has expected fillable attributes', function () {
    $fillable = (new User)->getFillable();

    expect($fillable)->toContain('name', 'email', 'password');
});

test('user hides sensitive attributes from serialization', function () {
    $hidden = (new User)->getHidden();

    expect($hidden)->toContain('password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token');
});

test('user uses TwoFactorAuthenticatable trait', function () {
    expect(in_array(TwoFactorAuthenticatable::class, class_uses_recursive(User::class)))->toBeTrue();
});

test('password cast is set to hashed', function () {
    $casts = (new User)->getCasts();

    expect($casts)->toHaveKey('password');
    expect($casts['password'])->toBe('hashed');
});

test('email_verified_at cast is set to datetime', function () {
    $casts = (new User)->getCasts();

    expect($casts)->toHaveKey('email_verified_at');
    expect($casts['email_verified_at'])->toBe('datetime');
});

test('two_factor_confirmed_at cast is set to datetime', function () {
    $casts = (new User)->getCasts();

    expect($casts)->toHaveKey('two_factor_confirmed_at');
    expect($casts['two_factor_confirmed_at'])->toBe('datetime');
});
