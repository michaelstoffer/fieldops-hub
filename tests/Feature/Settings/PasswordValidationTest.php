<?php

use App\Models\User;

test('password update requires current_password field', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from('/settings/password')
        ->put('/settings/password', [
            'current_password' => '',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ])
        ->assertSessionHasErrors('current_password');
});

test('password update requires new password field', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from('/settings/password')
        ->put('/settings/password', [
            'current_password' => 'password',
            'password' => '',
            'password_confirmation' => '',
        ])
        ->assertSessionHasErrors('password');
});

test('password update requires password confirmation to match', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from('/settings/password')
        ->put('/settings/password', [
            'current_password' => 'password',
            'password' => 'newpassword1',
            'password_confirmation' => 'newpassword2',
        ])
        ->assertSessionHasErrors('password');
});

test('password update requires current password to be correct', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->from('/settings/password')
        ->put('/settings/password', [
            'current_password' => 'wrong-password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ])
        ->assertSessionHasErrors('current_password');

    // Verify password was not changed
    expect(\Illuminate\Support\Facades\Hash::check('password', $user->fresh()->password))->toBeTrue();
});

test('unauthenticated password update is rejected', function () {
    $this->put('/settings/password', [
        'current_password' => 'password',
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
    ])->assertRedirect(route('login'));
});

test('password update is rate limited', function () {
    $user = User::factory()->create();

    for ($i = 0; $i < 6; $i++) {
        $this->actingAs($user)
            ->from('/settings/password')
            ->put('/settings/password', [
                'current_password' => 'password',
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ]);
    }

    $response = $this->actingAs($user)
        ->from('/settings/password')
        ->put('/settings/password', [
            'current_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

    $response->assertTooManyRequests();
});
