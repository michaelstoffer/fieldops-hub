<?php

namespace App\Listeners;

use App\Events\JobCreated;
use App\Services\MessageDispatcher;
use App\Services\TemplateRenderer;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendJobConfirmationSms implements ShouldQueue
{
    public function __construct(
        private readonly MessageDispatcher $dispatcher,
        private readonly TemplateRenderer $renderer,
    ) {}

    public function handle(JobCreated $event): void
    {
        $job = $event->job;
        $job->loadMissing('customer');

        $phone = $job->customer?->mobile ?? $job->customer?->phone;
        if (blank($phone)) {
            return;
        }

        $rendered = $this->renderer->render($job->organization_id, 'job_scheduled', 'sms', $job);
        if (! $rendered) {
            return;
        }

        $this->dispatcher->sendSms($job, 'job_scheduled', $phone, $rendered['body']);
    }
}
