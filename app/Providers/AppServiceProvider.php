<?php

namespace App\Providers;

use App\Events\JobCreated;
use App\Listeners\SendJobConfirmationEmail;
use App\Listeners\SendJobConfirmationSms;
use App\Services\SmsService;
use App\Services\TwilioSmsService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SmsService::class, TwilioSmsService::class);
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Event::listen(JobCreated::class, SendJobConfirmationEmail::class);
        Event::listen(JobCreated::class, SendJobConfirmationSms::class);
    }
}
