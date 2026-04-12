<?php

test('liveness endpoint returns 200 and ok status', function () {
    $this->getJson('/health')
        ->assertOk()
        ->assertJson(['status' => 'ok']);
});

test('readiness endpoint returns 200 when all checks pass', function () {
    $this->getJson('/health/ready')
        ->assertOk()
        ->assertJsonPath('status', 'ok')
        ->assertJsonStructure([
            'status',
            'checks' => ['database', 'queue', 'cache'],
            'timestamp',
        ]);
});

test('readiness endpoint checks are all ok in test environment', function () {
    $response = $this->getJson('/health/ready')->assertOk();
    expect($response->json('checks.database'))->toBe('ok');
    expect($response->json('checks.cache'))->toBe('ok');
});

test('health endpoints are accessible without authentication', function () {
    $this->getJson('/health')->assertOk();
    $this->getJson('/health/ready')->assertOk();
});
