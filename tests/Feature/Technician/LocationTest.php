<?php

use App\Events\DriverLocationUpdated;
use App\Models\DriverLocation;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;

function locationSetup(): User
{
    Role::firstOrCreate(['name' => 'technician', 'guard_name' => 'web']);
    $org  = Organization::factory()->create();
    $user = User::factory()->create(['organization_id' => $org->id]);
    $user->assignRole('technician');

    return $user;
}

test('location endpoint requires authentication', function () {
    $this->postJson('/api/technician/location', [
        'latitude'  => 40.7128,
        'longitude' => -74.0060,
    ])->assertUnauthorized();
});

test('technician can post a location update', function () {
    Event::fake();
    $user = locationSetup();

    $this->actingAs($user)
        ->postJson('/api/technician/location', [
            'latitude'  => 40.7128,
            'longitude' => -74.0060,
            'heading'   => 180.5,
            'speed'     => 15.0,
        ])
        ->assertCreated()
        ->assertJsonPath('data.user_id', $user->id)
        ->assertJsonPath('data.latitude', '40.7128000')
        ->assertJsonPath('data.longitude', '-74.0060000');

    $this->assertDatabaseHas('driver_locations', [
        'user_id'   => $user->id,
        'latitude'  => '40.7128000',
        'longitude' => '-74.0060000',
    ]);
});

test('location update fires DriverLocationUpdated broadcast event', function () {
    Event::fake();
    $user = locationSetup();

    $this->actingAs($user)
        ->postJson('/api/technician/location', [
            'latitude'  => 34.0522,
            'longitude' => -118.2437,
        ])
        ->assertCreated();

    Event::assertDispatched(DriverLocationUpdated::class, function ($event) use ($user) {
        return $event->location->user_id === $user->id;
    });
});

test('location update validates latitude range', function () {
    $user = locationSetup();

    $this->actingAs($user)
        ->postJson('/api/technician/location', [
            'latitude'  => 200,
            'longitude' => -74.0060,
        ])
        ->assertUnprocessable();
});

test('location update validates longitude range', function () {
    $user = locationSetup();

    $this->actingAs($user)
        ->postJson('/api/technician/location', [
            'latitude'  => 40.7128,
            'longitude' => 200,
        ])
        ->assertUnprocessable();
});

test('location update uses current time when recorded_at omitted', function () {
    Event::fake();
    $user = locationSetup();

    $this->actingAs($user)
        ->postJson('/api/technician/location', [
            'latitude'  => 40.7128,
            'longitude' => -74.0060,
        ])
        ->assertCreated();

    $location = DriverLocation::where('user_id', $user->id)->latest()->first();
    expect($location->recorded_at)->not->toBeNull();
});
