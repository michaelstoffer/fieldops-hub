<?php

namespace Tests;

use App\Services\SmsService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();

        // Prevent real Twilio calls during tests; individual tests may override.
        $this->app->bind(SmsService::class, fn () => new class implements SmsService {
            public function send(string $to, string $message): void {}
        });
    }
}
