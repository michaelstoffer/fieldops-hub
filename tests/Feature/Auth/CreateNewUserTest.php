<?php

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Validation\ValidationException;

test('CreateNewUser creates and returns a user', function () {
    $action = new CreateNewUser;

    $user = $action->create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBe('Jane Doe');
    expect($user->email)->toBe('jane@example.com');
    expect($user->getKey())->not->toBeNull();
});

test('CreateNewUser hashes the password', function () {
    $action = new CreateNewUser;

    $user = $action->create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'supersecret',
        'password_confirmation' => 'supersecret',
    ]);

    expect($user->password)->not->toBe('supersecret');
    expect(\Illuminate\Support\Facades\Hash::check('supersecret', $user->password))->toBeTrue();
});

test('CreateNewUser throws validation exception for missing name', function () {
    $action = new CreateNewUser;

    expect(fn () => $action->create([
        'name' => '',
        'email' => 'jane@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]))->toThrow(ValidationException::class);
});

test('CreateNewUser throws validation exception for invalid email', function () {
    $action = new CreateNewUser;

    expect(fn () => $action->create([
        'name' => 'Jane Doe',
        'email' => 'not-an-email',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]))->toThrow(ValidationException::class);
});

test('CreateNewUser throws validation exception for duplicate email', function () {
    User::factory()->create(['email' => 'existing@example.com']);

    $action = new CreateNewUser;

    expect(fn () => $action->create([
        'name' => 'Jane Doe',
        'email' => 'existing@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]))->toThrow(ValidationException::class);
});

test('CreateNewUser throws validation exception when password confirmation does not match', function () {
    $action = new CreateNewUser;

    expect(fn () => $action->create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password1',
        'password_confirmation' => 'password2',
    ]))->toThrow(ValidationException::class);
});

test('ResetUserPassword resets the password', function () {
    $user = User::factory()->create();
    $action = new \App\Actions\Fortify\ResetUserPassword;

    $action->reset($user, [
        'password' => 'brand-new-password',
        'password_confirmation' => 'brand-new-password',
    ]);

    expect(\Illuminate\Support\Facades\Hash::check('brand-new-password', $user->fresh()->password))->toBeTrue();
});

test('ResetUserPassword throws validation exception when passwords do not match', function () {
    $user = User::factory()->create();
    $action = new \App\Actions\Fortify\ResetUserPassword;

    expect(fn () => $action->reset($user, [
        'password' => 'brand-new-password',
        'password_confirmation' => 'something-else',
    ]))->toThrow(ValidationException::class);
});
