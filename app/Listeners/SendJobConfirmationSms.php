<?php

namespace App\Listeners;

use App\Events\JobCreated;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendJobConfirmationSms implements ShouldQueue
{
    public function __construct(private readonly SmsService $sms) {}

    public function handle(JobCreated $event): void
    {
        $job = $event->job;
        $job->loadMissing('customer');

        $phone = $job->customer->mobile ?? $job->customer->phone;

        if (blank($phone)) {
            return;
        }

        $scheduled = $job->scheduled_at
            ? $job->scheduled_at->format('D, M j \a\t g:i A')
            : 'TBD';

        $this->sms->send(
            $phone,
            "Your appointment \"{$job->title}\" is confirmed for {$scheduled}. Reply STOP to opt out.",
        );
    }
}
