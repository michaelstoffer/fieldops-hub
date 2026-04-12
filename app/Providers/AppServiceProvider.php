<?php

namespace App\Providers;

use App\Events\JobCreated;
use App\Events\JobStatusChanged;
use App\Listeners\SendJobConfirmationEmail;
use App\Listeners\SendJobConfirmationSms;
use App\Listeners\SendJobStatusMessages;
use App\Services\GeocodingService;
use App\Services\MessageDispatcher;
use App\Services\SmsService;
use App\Services\TemplateRenderer;
use App\Services\TwilioSmsService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SmsService::class, TwilioSmsService::class);

        $this->app->singleton(GeocodingService::class, fn () =>
            new GeocodingService(config('services.google.maps_api_key', ''))
        );

        $this->app->singleton(MessageDispatcher::class, fn ($app) =>
            new MessageDispatcher($app->make(SmsService::class))
        );

        $this->app->singleton(TemplateRenderer::class);
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        Event::listen(JobCreated::class, SendJobConfirmationEmail::class);
        Event::listen(JobCreated::class, SendJobConfirmationSms::class);
        Event::listen(JobStatusChanged::class, SendJobStatusMessages::class);
    }
}
